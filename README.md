# Forum API

REST API для форума. Построен на Laravel + Sanctum.

## Стек

- **PHP** 8.2+
- **Laravel** 11
- **Laravel Sanctum** — аутентификация через токены
- **MySQL**

---

## Переменные окружения

Обязательно настройте в `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forum
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATELESS_DOMAINS=localhost
```

---

## Аутентификация

API использует **Laravel Sanctum** (token-based). После логина вы получаете токен, который нужно передавать в заголовке каждого защищённого запроса:

```
Authorization: Bearer {your_token}
```

---

## Эндпоинты

### Auth

| Метод | URL | Защита | Описание |
|-------|-----|--------|----------|
| POST | `/api/register` | — | Регистрация |
| POST | `/api/login` | — | Вход, возвращает токен |
| POST | `/api/logout` | ✅ | Выход, инвалидирует токен |
| GET | `/api/me` | ✅ | Данные текущего пользователя |

**Пример регистрации:**
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Пример ответа на логин:**
```json
{
  "token": "1|abc123xyz...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

---

### Topics

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/api/topics` | Список топиков |
| POST | `/api/topics` | Создать топик |
| GET | `/api/topics/{topic}` | Получить топик |
| PUT/PATCH | `/api/topics/{topic}` | Обновить топик |
| DELETE | `/api/topics/{topic}` | Удалить топик |

**Пример создания топика:**
```http
POST /api/topics
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Как начать учить Laravel?",
  "description": "Делимся ресурсами и советами"
}
```

---

### Posts

Посты вложены в топики: `/api/topics/{topic}/posts`

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/api/topics/{topic}/posts` | Список постов топика |
| POST | `/api/topics/{topic}/posts` | Создать пост |
| GET | `/api/topics/{topic}/posts/{post}` | Получить пост |
| PUT/PATCH | `/api/topics/{topic}/posts/{post}` | Обновить пост |
| DELETE | `/api/topics/{topic}/posts/{post}` | Удалить пост |

---

### Comments

Комментарии вложены в посты: `/api/topics/{topic}/posts/{post}/comments`

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/api/topics/{topic}/posts/{post}/comments` | Список комментариев |
| POST | `/api/topics/{topic}/posts/{post}/comments` | Создать комментарий |
| GET | `/api/topics/{topic}/posts/{post}/comments/{comment}` | Получить комментарий |
| PUT/PATCH | `/api/topics/{topic}/posts/{post}/comments/{comment}` | Обновить комментарий |
| DELETE | `/api/topics/{topic}/posts/{post}/comments/{comment}` | Удалить комментарий |

---

### Likes

Лайки работают как переключатель: повторный запрос убирает лайк.

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/api/posts/{post}/like` | Лайк/анлайк поста |
| POST | `/api/comments/{comment}/like` | Лайк/анлайк комментария |

---

## Структура ответов

Успешный ответ:
```json
{
  "data": { ... }
}
```

Ошибка валидации (422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

Неавторизован (401):
```json
{
  "message": "Unauthenticated."
}
```