<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bewins Video Player</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-center">
      <h1 class="text-3xl font-bold text-white tracking-wide">🎬 Bewins</h1>
    </div>
  </header>

  <!-- Video Player Section -->
  <main class="flex-1 flex items-center justify-center p-4">
    <div class="w-full max-w-5xl bg-black rounded-xl overflow-hidden shadow-2xl">

      <!-- YouTube Video Player -->
      <div class="relative w-full" style="padding-bottom: 56.25%;">
        <iframe
          id="video-frame"
          class="absolute top-0 left-0 w-full h-full"
          src="https://www.youtube.com/embed/H8Lyj2D_cWo"
          title="YouTube video player"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
        </iframe>
      </div>

   

        <!-- Status Message -->
        <div id="status-message" class="hidden p-4 rounded-lg text-center max-w-2xl mx-auto"></div>


      </div>
    </div>
  </main>

    <script>
    // Auto-resize iframe for better responsiveness
    function resizeIframe() {
      const iframe = document.getElementById('video-frame');
      const container = iframe.parentElement;
      const containerWidth = container.offsetWidth;
      const containerHeight = containerWidth * 0.5625; // 16:9 aspect ratio
      container.style.height = containerHeight + 'px';
    }

    // Resize on load and window resize
    window.addEventListener('load', resizeIframe);
    window.addEventListener('resize', resizeIframe);
  </script>
</body>
</html>
