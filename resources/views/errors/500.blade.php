<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 — Server Error | Prosper Media</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
</head>
<body class="bg-pm-navy min-h-screen flex items-center justify-center font-body">
  <div class="absolute inset-0 grid-overlay pointer-events-none opacity-50"></div>

  <div class="relative z-10 text-center px-6 max-w-lg mx-auto">
    <p class="text-red-500/80 text-8xl font-black font-heading mb-4 leading-none">500</p>
    <h1 class="text-3xl font-extrabold text-white font-heading mb-4">Something Went Wrong</h1>
    <p class="text-gray-400 text-lg mb-10 leading-relaxed">
      Our team has been notified. Please try again in a moment.
    </p>
    <a href="/"
       class="inline-flex items-center gap-2 bg-pm-cyan text-white font-semibold
              px-6 py-3 rounded-xl hover:bg-cyan-500 transition-colors">
      ← Back to Home
    </a>
  </div>
</body>
</html>