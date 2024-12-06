        const loginForm = document.getElementById("loginForm");
        const registerForm = document.getElementById("registerForm");
        const formTitle = document.getElementById("formTitle");
        const regErrMsg = document.getElementById("registerErrorMsg");

        document.getElementById("showRegisterFormBtn").addEventListener("click", () => {
            formTitle.innerText = "Account Creation";
            loginForm.style.display = "none";
            registerForm.style.display = "flex";
            console.log(`Switching to registration: error msg: ${regErrMsg}`);
        });

        document.getElementById("showLoginFormBtn").addEventListener("click", () => {
            formTitle.innerText = "Log In";
            registerForm.style.display = "none";
            loginForm.style.display = "flex";
            regErrMsg.style.display = "none";
            console.log(`Switching to login: error msg: ${regErrMsg}`);
          
        });