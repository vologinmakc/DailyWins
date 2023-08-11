# DailyWins

## Filter Task

---

**1. DateFilter**

- **Описание**: Фильтрует задачи по дате создания.
- **Применение**: `/api/tasks?search[date]=YYYY-MM-DD`
- **Пример**: `/api/tasks?search[date]=2023-08-10` — вернёт все задачи, созданные 10 августа 2023 года.

---

**2. NameFilter**

- **Описание**: Фильтрует задачи по имени.
- **Применение**: `/api/tasks?search[name]=Some Name`
- **Пример**: `/api/tasks?search[name]=Meeting` — вернёт все задачи с именем, содержащим "Meeting".

---

**3. StartDateOrDayFilter**

- **Описание**: Фильтрует задачи либо по дате начала, либо по дню недели (для повторяющихся задач).
- **Применение**: `/api/tasks?search[start_date_or_day]=YYYY-MM-DD` или `/api/tasks?search[start_date_or_day]=DayOfWeekNumber`
- **Пример**:
    - `/api/tasks?search[start_date_or_day]=2023-08-10` — вернёт все задачи, начинающиеся 10 августа 2023 года.
    - `/api/tasks?search[start_date_or_day]=2` — вернёт все задачи, которые повторяются по вторникам.

---

**4. TypeFilter**

- **Описание**: Фильтрует задачи по типу (одноразовые или повторяющиеся).
- **Применение**: `/api/tasks?search[type]=TaskType`
- **Пример**:
    - `/api/tasks?search[type]=1` — вернёт все одноразовые задачи.
    - `/api/tasks?search[type]=2` — вернёт все повторяющиеся задачи.

---
