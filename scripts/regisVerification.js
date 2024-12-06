const regUname = document.getElementById("registerUsername");
const regPwd = document.getElementById("registerPassword");
const regRePwd = document.getElementById("re-enter-password");
const regBtn = document.getElementById("registerBtn");

const regUsrErrMsg = document.getElementById("usernameError");
const regPwdErrMsg = document.getElementById("pwdError");
const regRePwdErrMsg = document.getElementById("pwdReError");


regUname.addEventListener("input", event =>{
    if(event.target.value.length > 15 ||
        event.target.value.length < 3 &&
        event.target.value.length !== 0 
     ){
        regUsrErrMsg.style.display = "flex";
        regUsrErrMsg.innerText = "Username must be at least 3 and at most 15 characters long";
        regBtn.disabled = true;
    } else {
        regBtn.disabled = false;
        regUsrErrMsg.style.display = "none";
    }
}); 

regPwd.addEventListener("input", event =>{
    if(event.target.value.length > 16 ||
        event.target.value.length < 6 &&
        event.target.value.length !== 0 
     ){
        regPwdErrMsg.style.display = "flex";
        regPwdErrMsg.innerText = "Password must be at least 6 and at most 16 characters long";
        regBtn.disabled = true;
        regRePwd.disabled = true;
    } else {
        regBtn.disabled = false;
        regRePwd.disabled = false;
        regPwdErrMsg.style.display = "none";
    }
});
//Logic to ensure passwords are the same
regRePwd.addEventListener("input", event => {
    if(regPwd.value.length > 0){
        if(regPwd.value === event.target.value) {
            regBtn.disabled = false;
            regRePwdErrMsg.style.display = "none";
        } else {
            regRePwdErrMsg.style.display = "block";
            regRePwdErrMsg.innerText = "Passwords do not match";
            regBtn.disabled = true;
        }
    } 
});

