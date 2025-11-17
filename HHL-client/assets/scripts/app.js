document.addEventListener("DOMContentLoaded", () => {
  setupLoginForm();
});

function setupLoginForm() {
  const loginForm = document.getElementById("login-form");
  if (!loginForm) return;

  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const errorBox = document.getElementById("login-error");

  loginForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (errorBox) errorBox.textContent = "";

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    if (!email || !password) {
      if (errorBox) errorBox.textContent = "Email and password are required.";
      return;
    }

    try {
      const response = await fetch(
  "http://localhost/habit-health-logger/HHL-server/login.php",
  {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ email, password }),
  }
);

      const data = await response.json();

      if (!response.ok || data.status !== "ok") {
        if (errorBox) {
          errorBox.textContent = data.error || "Login failed.";
        }
        return;
      }

      localStorage.setItem("hhlUser", JSON.stringify(data.user));

      window.location.href = "dashboard.html";
    } catch (err) {
      console.error(err);
      if (errorBox) {
        errorBox.textContent = "Network error. Please try again.";
      }
    }
  });
}
