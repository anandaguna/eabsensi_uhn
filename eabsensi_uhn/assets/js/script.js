// Fungsi untuk validasi form registrasi
function validateRegisterForm() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;
    if (password !== confirmPassword) {
        alert("Password tidak cocok!");
        return false;
    }
    return true;
}
