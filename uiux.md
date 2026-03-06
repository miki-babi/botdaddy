Start with a **very simple UX** so users can create a bot in **2–3 minutes**.
Think of it like **“Create bot → choose template → customize → publish.”**

---

# 1. Main Dashboard

After login the user sees:

```
My Bots
----------------------
+ Create Bot

Bot Name        Users      Status
----------------------------------
My Store Bot    1,245      Active
Course Bot      340        Active
```

Actions per bot:

```
Open Dashboard
Edit
Broadcast
Analytics
```

---

# 2. Create Bot Flow

### Step 1 — Create Bot

User clicks **Create Bot**

Screen:

```
Create Telegram Bot
----------------------

1. Create bot in Telegram
2. Copy bot token
3. Paste it here

[ Bot Token Input ]

[ Continue ]
```

Link to Telegram BotFather so they can create the bot.

---

### Step 2 — Choose Template

```
Choose a Template
-----------------------

Link Hub Bot
Simple menu with buttons

Lead Collection Bot
Collect user contact

Store Bot
Sell digital products

FAQ Bot
Auto answer questions
```

User clicks **Use Template**.

---

### Step 3 — Customize Template

User edits simple fields.

Example (Store Bot):

```
Customize Your Bot
-----------------------

Bot Name
[ Axum Store Bot ]

Welcome Message
[ Welcome to Axum Store ]

Support Username
[ @axumsupport ]

Products Link
[ https://site.com/products ]

Button Text
[ View Products ]

[ Publish Bot ]
```

No complex logic yet.

---

# 3. Publish

User clicks **Publish**.

Your system:

```
save bot
set webhook
activate template
```

Success screen:

```
🎉 Your bot is live!

t.me/axum_store_bot

[ Open Bot ]
[ Go To Dashboard ]
```

---

# 4. Bot Dashboard

Each bot has its own dashboard.

```
Axum Store Bot
--------------------------------

Users: 1245
Joined Today: 34

[ Broadcast Message ]
[ Edit Content ]
[ View Users ]
[ Settings ]
```

---

# 5. Broadcast UI

```
Broadcast Message
---------------------

Message Text
[ Big sale today! ]

Image (optional)
[ Upload ]

Buttons (optional)
[ Add Button ]

Send To
☑ All users
☐ Last 24 hours
☐ Last 7 days

[ Send Broadcast ]
```

Messages sent through Telegram.

---

# 6. Edit Content

User changes template fields.

Example:

```
Edit Bot Content
----------------------

Welcome Message
Support Link
Button Text
Images
Links
```

Just update variables.

---

# 7. Users Page

```
Users
---------------------

Name          Joined
---------------------
John          2026-03-01
Sara          2026-03-02
David         2026-03-02
```

---

# 8. Settings

```
Bot Settings
----------------------

Bot Token
Webhook Status
Delete Bot
```

---

# 9. UX Rule (Very Important)

Avoid complexity early.

Good UX rule:

```
User must publish bot in under 2 minutes
```

---

# 10. Later (Advanced UI)

After launch you can add:

* Visual flow builder
* Template marketplace
* Automation triggers
* Funnels

Like ManyChat style builder.

---

✅ Your **MVP user flow** should be:

```
Signup
   ↓
Create Bot
   ↓
Paste Token
   ↓
Choose Template
   ↓
Customize Text
   ↓
Publish
   ↓
Broadcast / Manage Users
```

---
