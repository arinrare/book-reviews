
# Book Reviews Site in Laravel

## Local Address Details

URL:
```
http://book-reviews/hello
```

### Windows Hosts File

```text
# Added for laravel XAMPP app book-reviews
127.0.0.1   book-reviews
```

### XAMPP Apache VHOSTS File (E:/xampp/apache/conf/extra/httpd-vhosts.conf)

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "C:/xampp/htdocs"
    <Directory "C:/xampp/htdocs">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot "E:/xampp/htdocs/book-reviews/public"
    ServerName book-reviews
    <Directory "E:/xampp/htdocs/book-reviews/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## To Run

- Run Vite in the terminal for Tailiwnd ```npm run dev'''
- Launch XAMPP and start Apache server

