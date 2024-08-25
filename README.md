### Запуск проекта

```php
cp .env.test .env
docker-compose up --build -d
cd app
cp .env.test .env
```

Чтобы войти в любой из контейнеров, делаем следующее:
```php
docker exec -it <container_name> bash
```
После запуска контейнеров необходимо войти в контейнер php-cli
и выполнить команду создания базы данных + структуры

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Далее залить тестовые данные командой
```
php bin/console doctrine:fixtures:load
```

Данная команда создаст 3 пользователей

user@example.com
Пароль: user

admin@example.com
Пароль: admin

manager@example.com
Пароль: manager

Апи доступно по адресу
http://localhost:3000

Документация Swagger
http://localhost:3000/api/doc


Посмотреть запущенные контейнеры:
```php
docker ps
```

Логи контейнера:
```php
docker logs <container_name>
```