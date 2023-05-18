Система управления товарами
===========================

[![Смотрите видео на YouTube](https://img.youtube.com/vi/PHtleHgSbvA/0.jpg)](https://www.youtube.com/watch?v=PHtleHgSbvA)

Больше информации:
https://inverser.pro/200-sistema-upravleniya-tovarami-php
![](https://f.usemind.org/img/7/200-sistema-upravleniya-tovarami-php.jpg)

![](https://github.com/inverser-pro/productManageMe/blob/main/_TEST_DB_DELETE_AFTER_ADDED/2021-02-13_23-26.jpg?raw=true)

**ПО:**

PHP 7.2+ (PDO)\
VueJS 2+\
MySQL (MariaDB) 5.6+\
Bootstrap 4\
Date picker for pickadate.js v3.6.2\
jQuery

**Требование:**

Желательно наличие HTTPS соединения.
!! Запросы идут по fetch, а не по xmlHTTPRequest.

Есть небольшая защита от подделки запросов (постоянный токен).

**Возможности:**

Поддерживает добавление товаров. Выводит список имеющихся товаров в базе
данных в виде таблицы на VueJS (с моментальным поиском по этой таблице).

Поддерживает разграничение, установленное программистом на добавление
товара, просмотр оптовой цены. Две группы: администратор и продавец.
Администратор видит все поля и может удалять/добавлять продавцов.
Продавец НЕ видит оптовую цену, не может управлять пользователями.

Возможность добавлять поставщиков (только Администратор) и добавлять для
каждого из них свою цену в каждом товаре.
-----------
ВНИМАНИЕ! Все запросы Вам необходимо перенаправлять на index.php.
Для Apache необходимо создать файл `.htaccess`, размещённый в корне сайта (там, где все файлы из архива), со след. содержимым:
https://stackoverflow.com/questions/18406156/redirect-all-to-index-php-using-htaccess
~~~~
СМ. apache_htaccess_example.zip
~~~~
```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php?path=$1 [NC,L,QSA]
```
Для NGINX необходимо в конфигурации сервера и/или Вашего сайта добавить следующий код или поискать альтернативу
https://stackoverflow.com/questions/12924896/rewrite-all-requests-to-index-php-with-nginx
~~~~
СМ. nginx_conf_example.zip
~~~~
```
location / {
    set $page_to_view "/index.php";
    try_files $uri $uri/ @rewrites;
    root   /var/www/site;
    index  index.php index.html index.htm;
}

location ~ \.php$ {
    include /etc/nginx/fastcgi_params;
    fastcgi_pass  127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME /var/www/site$page_to_view;
}

# rewrites
location @rewrites {
    if ($uri ~* ^/([a-z]+)$) {
        set $page_to_view "/$1.php";
        rewrite ^/([a-z]+)$ /$1.php last;
    }
}
```
--------------
Логин по умолчанию:

admin@admin.com

Пароль:

**1234567**

1.  Отображение ранее добавленных товаров в виде таблицы:
    1.  ID.
    2.  Имя (наименование).
    3.  Цена.
    4.  Цена оптовая.
    5.  Количество.
    6.  Информация (краткая заметка).
    7.  Годен (до).
    8.  Добавлен (дата).
    9.  Добавил (ID администратора/продавца).
    10. Состояние (в наличии/нет в наличии).

2.  При редактировании товара Администратором:
    1.  Изменение всех вышеперечисленных данных.
    2.  При обновлении товара автоматически срок годности увеличивает с
        выбранной даны + 3 месяца.
    3.  Возможность добавлять поставщиков и отдельные цены для каждого
        поставщика (подгружается динамически).
    4.  Возможность добавления дат продаж товара (сколько продали — настолько количество товара сократилось). Добавление продажи:\
        1.  Дата.
        2.  Количество.
        3.  Комментарий.

3.  Управление пользователями (только Администратор):
    1.  Добавление и редактирование. Редактировать можно все, кроме
        логина (email), который должен быть уникальным для каждого
        нового пользователя.

4.  Добавление поставщика (только Администратор).
    1.  Название.
    2.  Краткий комментарий.
    3.  Действующий/не действующий.

Краткая инструкция по установке PHP-скрипта.
--------------------------------------------

1.  Скрипт должен корректно работать на web-хостинге, поддерживающем PHP
    v7.2 (возможно и ниже, но надо тестить).
2.  Версия MySQL (MariaDB) +- 5.6.34.
3.  Желательно, чтобы был подключен Apache (web-сервер), но в случае
    использования nginx, перенаправляйте все запросы на главную
    (index.php). 
4.  Для начала скопируйте или скачайте архив, который прикреплен выше.
5.  Разархивируйте его.
    1.  Вы увидите папку \_TEST\_DB\_DELETE\_AFTER\_ADDED, в которой
        находится тестовая версия базы данных. Импортируйте её через
        PHPMyAdmin или иным способом в необходимую (новую) базу данных
        на своем хостинге.
    2.  Обязательно удалите папку и её содержимое
        \_TEST\_DB\_DELETE\_AFTER\_ADDED, если Вы закинули её на
        хостинг.
    3.  Скопируйте в папку с Вашим доменом содержимое из архива (кроме
        вышеуказанной папки).
    4.  Измените настройки подключения к базе данных в
        файле `/loginme/loginmeInit.php`
    5.  А именно 12, 13, 14 строки:\
        define("L\_DB\_USER",'test\_user');\
        define("L\_DB\_PASS",'PASSWORD');\
        define("L\_DB\_NAME",'test\_name');
    6.  При заходе по адресу Вашего сайта должна открыться панель
        авторизации. Введите данные:\
        admin@admin.com\
        1234567
    7.  Обязательно измените данные администратора
        https://your-site.com/users  . Пароль должен быть от 7-ми
        символов.
    8.  Для теста в базе данных находится 4 товара.


