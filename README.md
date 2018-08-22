# Базовая информация по проекту

Для того, чтобы развернуть проект локально в корневом каталоге надо выполнить команду:

``make app.build.dev``

Для ее выполнения вам потребуется установленный Docker и docker-compose.
 После выполнения команды скопируйте содержимое .env.dist в файл .env

Для проверки на cs

``make app.check_style``

Для доступа в панель управления rabbitMQ:

``http://localhost:8810/#/queues/vhost/debit-queue``

Логин/пароль найдете в `.env.dist/.env`

Формат запроса:
В зависимости от очереди выставляется тип операции

debit-queue/credit-queue:
```
{
  "requestId": "57dbe1c0-f522-47d9-b1ab-7ff4039e3f82" (uuid4/unique) - используется для линковки с внешней системой
  "amount": 12345.987 - сумма операции
  "accountId": 2 (id аккаунта в системе)
}

```
transfer-queue:
```
{
  "requestId": "57dbe1c0-f522-47d9-b1ab-7ff4039e3f82" (uuid4/unique) - используется для линковки с внешней системой
  "amount": 12345.987 - сумма операции
  "from": 2 (id аккаунта отправителя в системе)
  "to": 3 (id аккаунта получателя в системе)
}
```

Операция трансфера представляет собой дебетование отправителя на сумму n и кредитование получателя на эту же сумму,
связь операций проходит через сквозной requestId.


transaction-update:
```
{
  "id": 20 - id транзакции в БД
  "status": "processing"/"processed"/"error" - доступные для смены статусы транзакции
}
```

## Запуск consumer'ов

DEBIT CREATE:

``bin/sf rabbitmq:consumer debit_create``

CREDIT CREATE:

``bin/sf rabbitmq:consumer credit_create``

TRANSFER CREATE:

``bin/sf rabbitmq:consumer transfer_create``

TRANSACTION UPDATE:

``bin/sf rabbitmq:consumer transaction_update``

###  Code style

Проверка

``make app.check_style``

Правка

``make app.app.phpcbf``

### Тесты

``make app.phpunit``

