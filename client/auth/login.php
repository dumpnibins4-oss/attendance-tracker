<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../css/styles.css" />
    <title>Attendance Tracker | Login</title>
</head>
<body>
    <div class="h-screen w-screen flex flex-col items-center justify-center bg-login">
        <img src="../public/assets/images/login-bg.png" alt="" class="fixed w-full h-full object-cover z-1" draggable="false" />
        
        <div class="flex flex-col items-center justify-start h-auto w-[500px] bg-white z-2 p-12 rounded-4xl shadow-lg border-1 border-[rgba(0,0,0,0.05)] gap-5">
            <div class="w-full h-auto flex flex-col items-center justify-center gap-2">
                <h1 class="text-3xl font-bold font-sans text-gray-800">Agent Login</h1>
                <p class="text-sm text-black/50">Enter your login credentials to continue</p>
            </div>

            <form action="" class="flex flex-col items-start justify-start w-full h-auto gap-4">
                <div class="w-full h-auto flex flex-col items-start justify-center gap-1">
                    <label for="biometricsID" class="text-sm text-black/50 font-medium ml-2 uppercase tracking-wide">Biometrics ID</label>
                    <input type="text" id="biometricsID" class="h-11 w-full border border-black/10 rounded-xl pl-4 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="Eg. 21312" required />
                </div>
                
                <div class="w-full h-auto flex flex-col items-start justify-center gap-1">
                    <label for="password" class="text-sm text-black/50 font-medium ml-2 uppercase tracking-wide">Password</label>
                    <input type="password" id="password" class="h-11 w-full border border-black/10 rounded-xl pl-4 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="•••••••••" required />
                </div>

                <div class="w-full h-auto flex flex-row items-center justify-between text-sm px-2">
                    <div class="flex items-center gap-1">
                        <p class="text-black/50">Forgot password?</p>
                        <a href="" class="text-blue-400 hover:text-blue-500 font-medium transition-all">Reset</a>
                    </div>
                </div>

                <button type="submit" class="w-full h-12 bg-orange-500 rounded-2xl text-white font-bold cursor-pointer hover:scale-105 hover:bg-orange-600 hover:shadow-lg transition-all active:scale-95">
                    Sign In
                </button>
            </form>

            <div class="flex items-center w-full gap-4 my-2">
                <div class="h-[1px] flex-1 bg-black/10"></div>
                <span class="text-[10px] text-black/30 font-bold uppercase tracking-widest">Or sign in with</span>
                <div class="h-[1px] flex-1 bg-black/10"></div>
            </div>

            <div class="relative group w-full h-12 rounded-2xl p-[2px] overflow-hidden transition-all duration-300 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/10"></div>

                <div class="absolute inset-[-1000%] animate-[spin_3s_linear_infinite] bg-[conic-gradient(#4285F4,#EA4335,#FBBC05,#34A853,#4285F4)] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <button type="button" class="relative w-full h-full bg-white rounded-[calc(1rem-1.5px)] flex items-center justify-center gap-3 hover:bg-gray-50 transition-all active:scale-95 cursor-pointer z-10">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5" alt="Google">
                    <span class="text-sm font-semibold text-black/70">Continue with Google</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>