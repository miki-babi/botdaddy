

Here is a **simple first version** that will work.

---

# 1. Store JSON in Database

Table:

```sql
bots
- id
- user_id
- bot_token
- flow_json
- created_at
```

Example `flow_json`:

```json
[
 {
  "id":"start",
  "trigger":"/start",
  "actions":[
   {"type":"send_message","text":"Welcome"},
   {"type":"buttons","items":[
     {"text":"Products","goto":"products"},
     {"text":"Support","goto":"support"}
   ]}
  ]
 },

 {
  "id":"products",
  "actions":[
   {"type":"send_message","text":"Here are our products"}
  ]
 },

 {
  "id":"support",
  "actions":[
   {"type":"send_message","text":"Contact @support"}
  ]
 }
]
```

---

# 2. One Webhook For All Bots

Instead of many routes:

```
/webhook/{bot_id}
```

Just use:

```
/telegram/webhook
```

When an update arrives from Telegram:

1. detect bot token
2. find bot in DB
3. load `flow_json`

---

# 3. Detect Trigger

Example logic:

```
if message == "/start"
   trigger = "/start"

if button clicked
   trigger = button_id
```

---

# 4. Find the Correct Block

Pseudo logic:

```
for block in flow_json:
    if block.trigger == trigger:
        run block
```

---

# 5. Run Actions

Example engine:

```
for action in block.actions:

    if action.type == "send_message":
        sendMessage()

    if action.type == "buttons":
        sendKeyboard()
```

---

# 6. Buttons Should Point To Blocks

Example button:

```
{
 "text":"Products",
 "goto":"products"
}
```

When clicked:

```
trigger = "products"
```

Then you open that block.

---

# 7. Very Simple First Templates

Start with **3 templates** only.

### Template 1 — Link Menu Bot

```
Start
 ↓
Welcome message
 ↓
Buttons with links
```

---

### Template 2 — Lead Capture Bot

```
Start
 ↓
Ask name
 ↓
Ask phone
 ↓
Save user
```

---

### Template 3 — Broadcast Bot

```
Start
 ↓
Subscribe user
 ↓
Admin can broadcast
```

---

# 8. Do NOT Build These Yet

Avoid early complexity:

❌ visual flow builder
❌ template marketplace
❌ complex automation
❌ AI replies

---

# 9. Build Only These First

Minimal system:

```
User dashboard
Create bot
Paste token
Choose template
Edit texts
Publish
```

Server handles everything.

---

