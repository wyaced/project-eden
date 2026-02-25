## **Eden**

### **Project Overview**
Eden is a web + SMS platform to help Filipino farmers optimize selling and trading of produce. 
Farmers can:
- Get market insights via SMS or Web
- View local listings
- List, buy, or trade produce with other farmers
- Interact through web (React) or SMS (Twilio)

> note: for contributing refer to the [Collaboration Workflow](#collaboration-workflow)
---

### **Tech Stack**

* **Frontend:** React (with Laravel Blade integration)
* **Backend:** Laravel 12
* **Database:** MySQL (via Docker)
* **Containerization:** Docker + DevContainer for collaboration
* **SMS Integration:** Twilio SDK
* **ML/Insights:** For market trends

---

### **Prerequisites**

* Docker & Docker Compose installed
* PHP 8.x, Composer (if running outside container)
* Node.js 18+ & npm (if running outside container)
* Twilio account (for SMS testing; virtual number for demo)
* ngrok (for local webhook testing)

---

### **Initial Setup**

1. **Clone the repository:**

```bash
git clone https://github.com/JLBuendicho/eden.git
cd eden
```

2. **Copy environment example file & set secrets:**

```bash
cp .env.example .env
# Update DB credentials, Twilio SID, Auth Token, Twilio number
```

3. **Start Docker containers (Laravel + MySQL + Redis if needed):**

```bash
docker-compose up -d --build
```

4. **Install Laravel dependencies (inside container):**

```bash
docker exec -it eden_app php artisan key:generate
docker exec -it eden_app php artisan migrate
```

5. **Start dev server (inside container):**

```bash
docker exec -it eden_app npm run dev
```

6. **Optional:** Start ngrok for Twilio SMS webhook testing

```bash
ngrok http 8000
```

* Copy the **ngrok URL** to your Twilio console webhook for inbound SMS

---

### **Running the Project**

* **Run dev server:**

``` bash
docker exec -it eden_app npm run dev
```

* **Web interface:** `http://localhost:8000`
* **React dev server (hot reload):** `http://localhost:5173`
* **Laravel logs (for SMS replies & demo):**

```bash
# run to view logs
docker exec -it eden_app tail -f storage/logs/laravel.log
```

* **Simulate SMS (Twilio):**

  * Send SMS to your virtual Twilio number
  * Replies will appear in Laravel logs

---

### **Collaboration Notes**

* All team members should use **DevContainer** for consistent environment:
  * VSCode → `Remote-Containers: Open Folder in Container`

* Push code to **GitHub** branch per feature:
  * follow git commit conventions

* Database migrations:
  * Run `docker exec -it php artisan migrate`

* React frontend hot reload works with `npm run dev`

---

### **Collaboration Workflow**
1. Fork the Repository

2. Clone your fork locally
``` bash
git clone https://github.com/<your-username>/eden.git
cd eden
```

3. Set the original repo as upstream
``` bash
git remote add upstream https://github.com/JLBuendicho/eden.git
git fetch upstream
```

4. Create a branch for your feature/bugfix:
``` bash
git checkout -b feature/<short-feature-name>
```

5. Make changes
    * Work inside the DevContainer for consistent environment
    * Test changes before committing

6. Commit your changes
``` bash
git add .
# example of commit following commit conventions
git commit -m "feat (insights): add SMS insight for given produce"
```

7. Push branch to your fork
``` bash
git push origin feature/<short-feature-name>
```

8. Open a Pull Request (PR)
    * Target branch: main on JLBuendicho/eden repo
    * Add description & submit for review

9. Sync your fork regularly:
``` bash
git fetch upstream
git checkout main
git merge upstream/main
git push origin main
```

#### Tips:
* Keep branches focused and short-lived
* Test inside DevContainer before opening PR
* Use descriptive commit messages