document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("login-form");

  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      // later: send credentials to backend via fetch()
      console.log("Login form submitted");
      // temporary redirect so you can see dashboard once we build it
      window.location.href = "dashboard.html";
    });
  }
});
