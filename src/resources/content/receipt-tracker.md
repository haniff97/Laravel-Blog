Receipt Tracker is an expense tracking application built around receipt uploads. It reads receipt images, saves the extracted information as transactions, and uses those records for spending dashboards and questions about expenses.

The project brings together a React interface, a FastAPI backend, local OCR, and external AI services. This article walks through the main capabilities and explains what happens behind the screens.

## A look at the application

The home screen provides entry points for scanning, receipts, the dashboard, and alerts. The other screens show how saved transactions become a monthly spending view or an answer to a question.

[![Receipt Tracker home screen with Scan, Receipt, Dashboard and Alerts](/images/receipt-tracker/media_1788027197658.png)](/images/receipt-tracker/media_1788027197658.png)

**01. Home screen.** Shortcuts to scanning and reviewing recorded expenses.

[![Dashboard showing monthly spending, category totals and individual transactions](/images/receipt-tracker/media_1788027197669.png)](/images/receipt-tracker/media_1788027197669.png)

**02. Dashboard.** Monthly comparisons, categories, and a transaction list.

[![A spending question answered with an RM43.46 dining transaction](/images/receipt-tracker/media_1788027197642.png)](/images/receipt-tracker/media_1788027197642.png)

**03. Spending questions.** An example answer with its transaction context.

Screenshots are from an earlier development session. Original browser controls, data, and status messages are retained.

## Scanning and reviewing receipts

A receipt starts as an uploaded image or PDF. With AI enabled, the backend first tries Gemini for image extraction. If that does not return a result, it uses Tesseract to read the text and can ask DeepSeek to interpret it. PDFs follow the OCR path and currently use only the first page.

The extracted merchant, date, and amount become a saved transaction. The interface then presents a confirmation form so those details can be corrected. That order matters: confirmation updates a record that already exists, so an unreviewed amount can appear in the totals.

AI-off mode still performs local OCR, with a simpler parser and manual correction available. Receipt images are stored on disk and linked to their database records. The current upload flow creates one transaction per receipt; individual line items are not stored separately.

## The spending dashboard

The dashboard summarises expenses for today, the week, and the month. It groups spending by month and category, while the transaction list supports category and time filters. Database queries calculate the main totals, independently of the AI models.

An AI-generated summary adds a written interpretation of the spending data. It is a separate layer over the recorded amounts, rather than the source of the dashboard totals.

[![Dashboard with zero spending totals, empty charts, and an AI summary about the lack of recorded spending](/images/receipt-tracker/media_1788027197700.png)](/images/receipt-tracker/media_1788027197700.png)

**04. Dashboard without recorded spending.** Daily, weekly, and monthly totals show RM0.00, with no chart entries or transactions. The AI summary discusses the absence of spending records. The original session also shows an upload-failed message.

## Asking about expenses in plain language

The question screen accepts prompts such as “What did I spend yesterday?” The backend sends DeepSeek a compact summary containing overall spending, category totals, monthly totals, and the latest 15 transactions. A response can include an answer and transaction context.

The model does not execute SQL or search the whole database. It interprets the supplied summary. If the model call fails, a keyword-based rules engine attempts an answer, although its vocabulary and date handling are limited.

A detailed historical question may ask for information absent from that summary. A plausible answer therefore still needs checking against the original records.

[![Question screen responding to Today transaction with no transactions available for today](/images/receipt-tracker/media_1788027197692.png)](/images/receipt-tracker/media_1788027197692.png)

**05. A question with no matching records.** The request for today's transactions returns a message that none are available. Compare this with screenshot 03, where the answer includes a matching expense. An upload-failed message is also visible in the original session.

## Spending alerts and relief categories

Unusual-spending alerts start with statistical comparisons within a category. The backend identifies candidate transactions, then optionally asks the model to review them and provide reasons. These alerts highlight outliers; they do not establish that a payment is fraudulent.

The relief view groups transactions by stored relief labels and compares amounts with configured annual caps. Labels can be edited in the interface. It helps organise records, but the stored categories and limits do not cover every eligibility condition.

## How the project fits together

React · FastAPI · SQLAlchemy · SQLite / PostgreSQL · Tesseract · Gemini / DeepSeek

The React frontend displays state and sends HTTP requests. FastAPI handles uploads, extraction, transaction queries, and model calls. SQLAlchemy manages database access, while receipt images live in file storage. Model API keys remain in the backend.

- The browser uploads a receipt to FastAPI.
- The backend extracts fields using image AI or OCR.
- A receipt record, image, and transaction are saved.
- The user reviews and edits the saved transaction.
- Dashboard queries include the updated expense.

The AI workflows use ordinary Python control flow around model calls. There is no vector database, document retrieval pipeline, or tool-calling loop in the current implementation. Processing runs inside HTTP requests rather than in a background job queue.

## What still needs work

The architecture guide documents several limitations alongside the working features:

- **Identity:** the application uses an installation-wide user value, with no individual authenticated accounts yet.

- **Cache freshness:** some AI caches use transaction count and latest date. Editing an amount can leave those values unchanged and produce a stale answer.

- **Scan accuracy:** extracted values need review. Duplicate detection depends on OCR text, and historical dates can be replaced by the current date on the AI path.

- **Currency and relief data:** currency settings change presentation without exchange-rate conversion. Relief caps are stored reference data rather than a live rules feed.

- **Backup and restore:** the SQLite backup includes the database and images, but is not a coordinated snapshot. Restoring replaces installation data and requires a restart.

The project connects receipt capture, editable records, and spending analysis in one application. It also illustrates where AI-assisted extraction is useful and where database queries, manual review, and careful handling of saved data remain necessary.

---

Based on the Receipt Tracker architecture guide, reviewed on 18 October 2026.
