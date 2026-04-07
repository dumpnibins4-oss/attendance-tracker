
<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/styles.css" />
    <title>Attendance Tracker | Login</title>
    <style type="text/tailwindcss">
        @import "tailwindcss";
        @theme {
            --color-mainp: #E8EAEC;
            --color-login: #F5F2EA;
            --color-dark-primary: #1C1C1C;
            --color-accent: var(--accent-color, #f97316);
            --color-accent-hover: var(--accent-color-hover, #ea580c);
        }
        @custom-variant dark (&:where(.dark, .dark *));
    </style>
</head>
<body>
    <div class="h-screen w-screen flex flex-col items-center justify-center bg-mainp">        
        <div class="flex flex-col items-center justify-start h-auto w-[500px] bg-zinc-700 z-2 p-12 rounded-4xl shadow-lg border-1 border-[rgba(0,0,0,0.05)] gap-5">
            <div class="w-full h-auto flex flex-col items-center justify-center gap-2">
                <div class="w-auto h-auto flex items-center justify-center">
                    <img src="../public/assets/images/logo.png" alt="logo" class="h-8 w-auto" />
                </div>
                <h1 class="text-3xl font-bold font-sans text-accent">Agent Login</h1>
                <p class="text-sm text-white/50">Enter your login credentials to continue</p>
            </div>

            <!-- LOGIN FORM -->
            <form id="login-form" class="flex flex-col items-start justify-start w-full h-auto gap-4">
                <div class="w-full h-auto flex flex-col items-start justify-center gap-1">
                    <label for="biometricsID" class="text-sm text-white/50 font-medium ml-2 uppercase tracking-wide">Biometrics ID</label>
                    <input type="text" id="biometricsID" name="biometricsID" class="h-11 w-full border border-white/20 rounded-xl pl-4 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-white/50 text-white" placeholder="Eg. 21312" required />
                </div>
                <div class="w-full h-auto flex flex-col items-start justify-center gap-1">
                    <label for="password" class="text-sm text-white/50 font-medium ml-2 uppercase tracking-wide">Password</label>
                    <div class="flex flex-row items-center justify-start w-full h-11 focus-within:ring-2 focus-within:ring-blue-500 border border-white/20 rounded-xl pl-4 transition-all overflow-hidden">
                        <input type="password" id="password" name="password" class="focus:outline-none h-full flex-1 placeholder:text-white/50 text-white" placeholder="•••••••••" required />
                        <button
                            type="button"
                            class="h-11 w-11"
                            onclick="togglePassword()"
                        >
                            <i id="toggleIcon" class="fa-slab fa-regular fa-eye-slash text-white/50"></i>
                        </button>
                    </div>
                </div>
                <div class="w-full h-auto flex flex-row items-center justify-between text-sm px-2">
                    <div class="flex items-center gap-1">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 border border-white/20 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" />
                        <label for="remember" class="text-white/50">Remember me</label>
                    </div>
                    <div class="flex items-center gap-1">
                        <p class="text-white/50">Forgot password?</p>
                        <a href="" class="text-blue-400 hover:text-blue-500 font-medium transition-all">Reset</a>
                    </div>
                </div>

                <button type="submit" class="w-full h-12 bg-accent rounded-2xl text-white font-bold cursor-pointer hover:scale-105 hover:bg-accent-hover hover:shadow-lg transition-all active:scale-95">
                    Sign In
                </button>
            </form>

            <div class="flex items-center w-full gap-4 my-2">
                <div class="h-[1px] flex-1 bg-white/20"></div>
                <span class="text-[10px] text-white/50 font-bold uppercase tracking-widest">Or sign in with</span>
                <div class="h-[1px] flex-1 bg-white/20"></div>
            </div>

            <div class="relative group w-full h-12 rounded-2xl p-[2px] overflow-hidden transition-all duration-300 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/10"></div>

                <div class="absolute inset-[-1000%] animate-[spin_3s_linear_infinite] bg-[conic-gradient(#4285F4,#EA4335,#FBBC05,#34A853,#4285F4)] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <button type="submit" class="relative w-full h-full bg-white rounded-[calc(1rem-1.5px)] flex items-center justify-center gap-3 hover:bg-gray-50 transition-all active:scale-95 cursor-pointer z-10">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5" alt="Google">
                    <span class="text-sm font-semibold text-black/70">Continue with Google</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
<script>
    // Set accent color from localStorage
    const accentBase = localStorage.accent || '#f97316'; // Default orange-500
    const accentHover = localStorage.accentHover || '#ea580c'; // Default orange-600
    document.documentElement.style.setProperty('--accent-color', accentBase);
    document.documentElement.style.setProperty('--accent-color-hover', accentHover);
</script>
<script>
    document.getElementById('login-form').addEventListener('submit', async function (e) {
        e.preventDefault()

        try {
            const res = await fetch("../../server/api/login_api.php", {
                method: "POST",
                body: new FormData(this),
            })
            const data = await res.json()
            if (data.success) {
                Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                }).fire({
                    icon: "success",
                    title: "Signed in successfully"
                }).then(() => {
                    window.location.href = "../index.php"
                })
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message,
                    icon: "error"
                })
            }
        } catch (err) {
            Swal.fire({
                title: "Error!",
                text: "Something went wrong, please try again.",
                icon: "error"
            })
        }
    })

    // SHOW/HIDE PASSWORD
    function togglePassword() {
        const passwordInput = document.getElementById('password')
        const icon = document.getElementById('toggleIcon')
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text'
            icon.classList.replace('fa-eye-slash', 'fa-eye')
        } else {
            passwordInput.type = 'password'
            icon.classList.replace('fa-eye', 'fa-eye-slash')
        }
    }
</script>
