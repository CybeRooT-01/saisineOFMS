<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Saisine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #fff5f0 0%, #ffffff 50%, #fff8f5 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 85, 0, 0.1);
        }

        .input-focus-effect:focus {
            box-shadow: 0 0 0 3px rgba(255, 85, 0, 0.1);
        }

        #particles-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen relative overflow-hidden">
    <!-- Interactive Particles Canvas -->
    <canvas id="particles-canvas"></canvas>

    <!-- Login Container -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full">
            <!-- Title -->
            <div class="text-center mb-10">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">
                    Bienvenue
                </h2>
                <p class="text-gray-600">Connectez-vous à votre espace Saisine</p>
            </div>

            <!-- Login Form Card -->
            <div class="glass-effect rounded-2xl shadow-2xl p-8 space-y-6">
                <form method="POST" action="/login" class="space-y-6">
                    @csrf
                    
                    <!-- Login Input -->
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                            Login Windows
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input 
                                id="login" 
                                name="login" 
                                type="text" 
                                autocomplete="username" 
                                required 
                                value="{{ old('login') }}"
                                class="input-focus-effect block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('login') border-red-500 @enderror" 
                                placeholder="votre.identifiant"
                            >
                        </div>
                        @error('login')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                autocomplete="current-password" 
                                required 
                                class="input-focus-effect block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror" 
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded transition-all duration-200"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Se souvenir de moi
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 text-base font-medium rounded-xl text-white shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 transform hover:scale-[1.02]"
                        style="background: linear-gradient(90deg, #ff5500 0%, #ff6a1a 100%);"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Se connecter
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-orange-800">
                            <p class="font-medium mb-1">Connexion via LDAP</p>
                            <p class="text-orange-700">Utilisez vos identifiants windows pour vous connecter.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>© 2026 Saisine - Tous droits réservés</p>
            </div>
        </div>
    </div>

    <script>
        // Interactive Particle System
        const canvas = document.getElementById('particles-canvas');
        const ctx = canvas.getContext('2d');
        
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        let particles = [];
        let mouse = { x: null, y: null, radius: 150 };

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            init();
        });

        window.addEventListener('mousemove', (e) => {
            mouse.x = e.x;
            mouse.y = e.y;
        });

        class Particle {
            constructor(x, y, size, color, speedX, speedY) {
                this.x = x;
                this.y = y;
                this.size = size;
                this.baseSize = size;
                this.color = color;
                this.speedX = speedX;
                this.speedY = speedY;
                this.density = (Math.random() * 30) + 1;
            }

            draw() {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.closePath();
                ctx.fill();
            }

            update() {
                // Check collision with mouse
                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                let forceDirectionX = dx / distance;
                let forceDirectionY = dy / distance;
                let maxDistance = mouse.radius;
                let force = (maxDistance - distance) / maxDistance;
                let directionX = forceDirectionX * force * this.density;
                let directionY = forceDirectionY * force * this.density;

                if (distance < mouse.radius) {
                    this.x -= directionX;
                    this.y -= directionY;
                    this.size = this.baseSize * 1.5;
                } else {
                    this.size = this.baseSize;
                }

                // Move particles
                this.x += this.speedX;
                this.y += this.speedY;

                // Bounce off edges
                if (this.x > canvas.width || this.x < 0) {
                    this.speedX = -this.speedX;
                }
                if (this.y > canvas.height || this.y < 0) {
                    this.speedY = -this.speedY;
                }

                this.draw();
            }
        }

        function init() {
            particles = [];
            const numberOfParticles = Math.floor((canvas.width * canvas.height) / 5000);
            
            for (let i = 0; i < numberOfParticles; i++) {
                let size = (Math.random() * 5) + 1;
                let x = Math.random() * canvas.width;
                let y = Math.random() * canvas.height;
                let speedX = (Math.random() * 0.8) - 0.4;
                let speedY = (Math.random() * 0.8) - 0.4;
                
                // Orange color variations
                const colors = [
                    'rgba(255, 85, 0, 0.8)',
                    'rgba(255, 106, 26, 0.7)',
                    'rgba(255, 140, 60, 0.6)',
                    'rgba(255, 85, 0, 0.9)',
                    'rgba(255, 120, 40, 0.7)'
                ];
                let color = colors[Math.floor(Math.random() * colors.length)];
                
                particles.push(new Particle(x, y, size, color, speedX, speedY));
            }
        }

        function connect() {
            for (let a = 0; a < particles.length; a++) {
                for (let b = a; b < particles.length; b++) {
                    let dx = particles[a].x - particles[b].x;
                    let dy = particles[a].y - particles[b].y;
                    let distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 150) {
                        let opacity = 1 - (distance / 150);
                        ctx.strokeStyle = `rgba(255, 85, 0, ${opacity * 0.5})`;
                        ctx.lineWidth = 1.5;
                        ctx.beginPath();
                        ctx.moveTo(particles[a].x, particles[a].y);
                        ctx.lineTo(particles[b].x, particles[b].y);
                        ctx.stroke();
                    }
                }
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
            }
            
            connect();
            requestAnimationFrame(animate);
        }

        init();
        animate();
    </script>
</body>
</html>
