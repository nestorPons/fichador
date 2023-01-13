# fichador
Sistema WEB de registro de la jornada laboral 

## Requisitos 
  Si se despliega en un servidor publico se requiere de base de datos MYSQL o MARIADB en el servidor
  Si se despliega en local se requiere DOCKER

## Características
  Se puede desplegar en local con persistencia mediante un servidor LAMP alojado en la carpeta `.server_v1.1`

## Despliegue
1. Configurar parametros de .env y subir a servidor
2. Desplegar carpetas httdocs(public) y app/fichador(aplicación) en vuestro servidor,
  o desplegar en local mediante:
  1. `cd .server_v1.1`
  2. `sudo docker-compose up -d`
3. Acceder a la url del proyecto o http://localhost:80 y http://localhost:8080 para acceder a phpmyadmin
