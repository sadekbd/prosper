<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 — Page Not Found | Prosper Media</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
</head>
<body class="bg-pm-navy min-h-screen flex items-center justify-center font-body">
  <div class="absolute inset-0 grid-overlay pointer-events-none opacity-50"></div>
  <div class="absolute top-0 right-0 w-96 h-96 bg-pm-cyan/5 rounded-full blur-3xl"></div>

  <div class="relative z-10 text-center px-6 max-w-lg mx-auto">
    <p class="text-pm-cyan text-8xl font-black font-heading mb-4 leading-none">404</p>
    <h1 class="text-3xl font-extrabold text-white font-heading mb-4">Page Not Found</h1>
    <p class="text-gray-400 text-lg mb-10 leading-relaxed">
      The page you're looking for doesn't exist or has been moved.
      Let's get you back on track.
    </p>
    <div class="flex flex-wrap items-center justify-center gap-4">
      <a href="/"
         class="inline-flex items-center gap-2 bg-pm-cyan text-white font-semibold
                px-6 py-3 rounded-xl hover:bg-cyan-500 transition-colors">
        ← Back to Home
      </a>
      <a href="/contact"
         class="inline-flex items-center gap-2 border-2 border-white/20 text-white font-semibold
                px-6 py-3 rounded-xl hover:border-white/40 transition-colors">
        Contact Us
      </a>
    </div>
    <div class="mt-12 pt-8 border-t border-white/10">
      <a href="/" class="flex items-center justify-center gap-3">
        <div class="w-8 h-8 bg-pm-cyan rounded-lg flex items-center justify-center">
          <span class="text-white font-black font-heading">P</span>
        </div>
        <span class="text-white font-bold font-heading">Prosper<span class="text-pm-cyan">Media</span></span>
      </a>
    </div>
  </div>
</body>
</html>