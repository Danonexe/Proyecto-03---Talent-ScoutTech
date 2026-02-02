# Informe de Auditoría de Seguridad Web – Talent ScoutTech

## 1. Fundamentación teórica

Una auditoría de seguridad web es un proceso sistemático orientado a identificar, analizar y explotar de forma controlada vulnerabilidades presentes en aplicaciones y servicios web, con el objetivo de evaluar su nivel real de exposición frente a ataques. Este tipo de auditoría abarca tanto componentes técnicos (código, configuración, servidores, bases de datos) como aspectos lógicos (flujos de negocio, control de acceso y gestión de sesiones).

Para la realización de esta auditoría se han utilizado metodologías ampliamente aceptadas a nivel internacional, principalmente **OWASP Web Security Testing Guide (WSTG)** y **OWASP Top 10**, complementadas con técnicas de pentesting manual. Estas metodologías permiten estructurar el análisis en fases (reconocimiento, identificación, explotación y mitigación) y asegurar una cobertura completa de los riesgos más relevantes.

El enfoque seguido combina pruebas **black-box** (sin conocimiento previo del código) y **grey-box** cuando el enunciado lo permite, simulando escenarios realistas de ataque. El resultado final no solo es la identificación de vulnerabilidades, sino también la propuesta de **contramedidas prácticas**, alineadas con buenas prácticas de desarrollo seguro (Secure SDLC).

## 2. Objetivos del proyecto

* Identificar vulnerabilidades críticas en la aplicación web Talent ScoutTech.
* Explotar de forma controlada dichas vulnerabilidades para demostrar su impacto real.
* Analizar los mecanismos de autenticación, control de acceso y gestión de sesiones.
* Evaluar la exposición de la aplicación frente a ataques SQLi, XSS y CSRF.
* Proponer e implementar medidas de mitigación basadas en OWASP.
* Elaborar un informe técnico claro, justificado y reproducible.

## 3. Descripción del proyecto

Talent ScoutTech es una aplicación web desarrollada por ACME para la gestión y evaluación de talento deportivo. Permite a usuarios autenticados registrar jugadores, editar su información deportiva y añadir comentarios de evaluación. La aplicación gestiona información sensible tanto de usuarios como de jugadores, por lo que la seguridad resulta un aspecto crítico.

El objetivo de esta auditoría es evaluar si los mecanismos de protección implementados son suficientes y detectar posibles fallos que permitan comprometer la confidencialidad, integridad o disponibilidad de la información.

---

# Parte 1 – SQL Injection (SQLi)

## a) Identificación de la vulnerabilidad

Se analiza el formulario de autenticación, introduciendo caracteres especiales como comillas simples (`'`) en los campos de usuario y contraseña. Por ejemplo:

* **Usuario:** `'`
* **Contraseña:** `test`

La aplicación devuelve un error SQL visible, lo que indica que la entrada del usuario se está concatenando directamente en la consulta.

A partir del mensaje de error se deduce una consulta del tipo:

```sql
SELECT * FROM users WHERE username = '$user' AND password = '$password';
```

El error demuestra que ambos campos son utilizados en la consulta sin validación adecuada, siendo el campo *username* el principal vector de inyección.

## b) Ataque mediante diccionario

Conociendo que el formulario es vulnerable y el nombre de los campos, se plantea un ataque de fuerza bruta lógico combinando SQLi con un diccionario de contraseñas comunes.

Payload utilizado en el campo usuario:

```sql
' OR 1=1 --
```

Y se prueban las contraseñas del diccionario en el campo password. Alternativamente, se utiliza:

```sql
' OR password='1234' --
```

Este ataque permite autenticarse como el primer usuario devuelto por la base de datos (en este caso, **luis**), sin conocer previamente los usuarios registrados.

## c) Análisis de SQLite3::escapeString()

Aunque en `areUserAndPasswordValid()` se utiliza `SQLite3::escapeString()`, el error de programación reside en que:

* Se escapan los valores, pero **se siguen concatenando directamente en la consulta SQL**.
* No se utilizan **consultas preparadas (prepared statements)**.

La corrección adecuada consiste en utilizar `prepare()` y `bindValue()`, evitando por completo la interpretación de código SQL inyectado.

## d) Vulnerabilidad en add_comment.php~

El archivo de backup `add_comment.php~` es accesible y muestra el código fuente. En él se observa que el identificador de usuario se recibe desde un parámetro POST sin validación ni comprobación de sesión.

La vulnerabilidad es un **IDOR + falta de control de acceso**, que permite enviar comentarios en nombre de otros usuarios modificando el parámetro `user_id`.

---

# Parte 2 – Cross Site Scripting (XSS)

## a) XSS almacenado en comentarios

Se introduce el siguiente comentario:

```html
<script>alert('XSS')</script>
```

Al visualizar los comentarios en `show_comments.php`, el script se ejecuta, confirmando un **XSS almacenado**.

## b) Uso de &amp; en enlaces

Dentro del código HTML, el carácter `&` debe representarse como `&amp;` para evitar ambigüedades en el parseo del documento. Esto no afecta a la URL real, pero sí a cómo el navegador interpreta el HTML, siendo una medida de correcta codificación, no de seguridad por sí misma.

## c) Problema en show_comments.php

El problema principal es que los comentarios se imprimen directamente sin aplicar **escape de salida**. La solución consiste en usar funciones como `htmlspecialchars()` antes de mostrar cualquier entrada del usuario.

## d) Otras páginas afectadas

Mediante la inserción del mismo payload XSS en distintos campos se detecta que `list_players.php` también es vulnerable. El método utilizado fue introducir contenido HTML/JS y comprobar su ejecución.

---

# Parte 3 – Control de acceso, autenticación y sesiones

## a) Seguridad del registro

Medidas propuestas:

* Validación de entradas (longitud, formato).
* Hash de contraseñas con `password_hash()`.
* Protección contra registros automatizados (CAPTCHA).
* Eliminación de mensajes de error detallados.

## b) Seguridad del login

* Uso de hash y verificación con `password_verify()`.
* Limitación de intentos de login.
* Mensajes de error genéricos.
* Regeneración del ID de sesión tras login.

## c) Gestión del acceso a register.php

Al no permitir auto-registro, se restringe el acceso mediante:

* Comprobación de rol administrador.
* Eliminación del enlace público.
* Protección mediante `.htaccess`.

## d) Protección de la carpeta private

En entorno local es accesible. Medidas:

* Configuración del servidor para denegar acceso.
* Ubicar la carpeta fuera del DocumentRoot.
* Reglas `.htaccess` con `Deny from all`.

## e) Gestión de sesiones

Se detecta que la sesión no regenera el ID tras autenticación. Esto permite **session fixation**. Solución:

* `session_regenerate_id(true)` tras login.
* Uso de cookies `HttpOnly` y `Secure`.
* Tiempo de expiración de sesión.

---

# Parte 4 – Seguridad del servidor web

## Inventario y medidas

* **Apache**: desactivar módulos innecesarios, ocultar versión.
* **PHP**: `display_errors=Off`, `allow_url_include=Off`.
* **Base de datos SQLite**: permisos restrictivos.
* **Sistema operativo**: actualizaciones periódicas.
* **Logs**: monitorización y alertas.

---

# Parte 5 – CSRF

## a) Botón Profile

Se modifica la vista del jugador para incluir un formulario HTML con acción al enlace malicioso proporcionado.

## b) Ataque sin interacción

Se introduce un comentario con una etiqueta `<img>` cuyo atributo `src` apunta a la URL de donación, ejecutándose automáticamente al cargar la página.

## c) Condición para la donación

El usuario debe:

* Estar autenticado en web.pagos.
* Tener sesión activa y saldo suficiente.

## d) Ataque usando POST

El uso de POST no elimina el CSRF. Se puede crear un formulario oculto que se envíe automáticamente mediante JavaScript al cargar la página.

---

# Conclusiones

La aplicación Talent ScoutTech presenta múltiples vulnerabilidades críticas (SQLi, XSS, CSRF, control de acceso deficiente) que permiten comprometer usuarios y datos. La implementación de las medidas propuestas reduciría drásticamente la superficie de ataque y alinearía la aplicación con los estándares de seguridad actuales.

