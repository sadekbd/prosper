<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maintenance Mode | Prosper Media</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css'])
</head>
<body class="bg-pm-navy min-h-screen flex items-center justify-center font-body">
  <div class="absolute inset-0 grid-overlay pointer-events-none opacity-50"></div>
  <div class="absolute top-0 left-0 w-96 h-96 bg-pm-gold/5 rounded-full blur-3xl"></div>

  <div class="relative z-10 text-center px-6 max-w-lg mx-auto">
    <div class="w-20 h-20 bg-pm-gold/10 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-pm-gold/20">
      <svg class="w-10 h-10 text-pm-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
    </div>
    <h1 class="text-3xl font-extrabold text-white font-heading mb-4">We'll Be Right Back</h1>
    <p class="text-gray-400 text-lg mb-3 leading-relaxed">
      Prosper Media is undergoing scheduled maintenance.
    </p>
    <p class="text-pm-gold text-sm font-semibold">Be Optimistic — we'll be back shortly.</p>
  </div>
</body>
</html>