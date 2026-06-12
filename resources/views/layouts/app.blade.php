<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    <meta name="vapid-public-key" content="{{ env('VAPID_PUBLIC_KEY') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('main-logo.svg') }}">


    <meta name="theme-color" content="#ffffff">
    <!--<link rel="manifest" href="/manifest.webmanifest">-->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/apple-icon-180.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-status-bar-style" content="default">
    <meta name="mobile-web-app-title" content="SiBanksa">
    @routes
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
    @include('link.headlink')
     <style>
     #splash-native {
        position: fixed;
        inset: 0;
        background: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
    }
    #splash-native.hide {
        opacity: 0;
        pointer-events: none;
    }
    #app {
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    #app.ready {
        opacity: 1;
    }
    #splash-native p {
        color: #10b981;
        margin-top: 12px;
        font-size: 22px;
        font-weight: 800;
        font-family: sans-serif;
        animation: fadeUp 0.6s ease both;
        animation-delay: 0.2s;
    }
    #splash-native small {
        color: #6b7280;
        font-size: 13px;
        font-family: sans-serif;
        animation: fadeUp 0.6s ease both;
        animation-delay: 0.3s;
    }
    #splash-native svg {
        animation: fadeUp 0.6s ease both;
    }
    .splash-dots {
        display: flex;
        gap: 6px;
        margin-top: 32px;
        animation: fadeUp 0.6s ease both;
        animation-delay: 0.4s;
    }
    .splash-dots span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        animation: dot-pulse 1.2s ease-in-out infinite;
    }
    .splash-dots span:nth-child(2) { animation-delay: 0.2s; }
    .splash-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes dot-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.3; transform: scale(0.7); }
    }
              .cls-1 {
        fill: url(#linear-gradient-8);
      }

      .cls-2 {
        fill: url(#linear-gradient-7);
      }

      .cls-3 {
        fill: url(#linear-gradient-5);
      }

      .cls-4 {
        fill: url(#linear-gradient-6);
      }

      .cls-5 {
        fill: url(#linear-gradient-9);
      }

      .cls-6 {
        fill: url(#linear-gradient-4);
      }

      .cls-7 {
        fill: url(#linear-gradient-3);
      }

      .cls-8 {
        fill: url(#linear-gradient-2);
      }

      .cls-9 {
        fill: url(#linear-gradient);
      }

      .cls-10 {
        fill: url(#linear-gradient-10);
      }

      .cls-11 {
        fill: url(#linear-gradient-11);
      }

      .cls-12 {
        fill: url(#linear-gradient-12);
      }

      .cls-13 {
        fill: url(#linear-gradient-13);
      }

      .cls-14 {
        fill: url(#linear-gradient-19);
      }

      .cls-15 {
        fill: url(#linear-gradient-14);
      }

      .cls-16 {
        fill: url(#linear-gradient-21);
      }

      .cls-17 {
        fill: url(#linear-gradient-20);
      }

      .cls-18 {
        fill: url(#linear-gradient-22);
      }

      .cls-19 {
        fill: url(#linear-gradient-23);
      }

      .cls-20 {
        fill: url(#linear-gradient-25);
      }

      .cls-21 {
        fill: url(#linear-gradient-15);
      }

      .cls-22 {
        fill: url(#linear-gradient-16);
      }

      .cls-23 {
        fill: url(#linear-gradient-17);
      }

      .cls-24 {
        fill: url(#linear-gradient-18);
      }

      .cls-25 {
        fill: url(#linear-gradient-24);
      }

      .cls-26 {
        fill: url(#linear-gradient-26);
      }
    </style>
</head>

<body x-data="{
    ...themeData(),
    sidebarToggle: false,
    open: false,
    selected: null,
    page: 'dashboard',
    mobileMenuOpen: false,
    showForm: '{{ old('id_roles') == 3 ? 'Nasabah' : 'BankSampah' }}',
    loaded: true,
    stickyMenu: false,
    scrollTop: false
}" x-init="initTheme()"
    class="bg-gray-100 dark:bg-gray-900 min-h-screen font-[Poppins] antialiased">

    <div class=" flex bg-white dark:bg-gray-900" id="splash-native">
                      <svg class="w-24 h-24" viewBox="0 0 500 500" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
  <defs>
    <linearGradient id="linear-gradient" x1="129.22" y1="74.04" x2="266.92" y2="74.04" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#2bb673"/>
      <stop offset=".44" stop-color="#006a3a"/>
      <stop offset="1" stop-color="#006838"/>
    </linearGradient>
    <linearGradient id="linear-gradient-2" x1="177.36" y1="381.05" x2="321.84" y2="381.05" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-3" x1="224.87" y1="119.56" x2="338.92" y2="119.56" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-4" x1="194.39" y1="175.47" x2="262.64" y2="175.47" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-5" x1="160.35" y1="118.21" x2="272.8" y2="118.21" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-6" x1="226.32" y1="168.9" x2="304.67" y2="168.9" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-7" x1="224.54" y1="72.75" x2="370.95" y2="72.75" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-8" x1="258.27" y1="206.1" x2="395.97" y2="206.1" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-9" x1="353.92" y1="251.62" x2="467.97" y2="251.62" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-10" x1="323.44" y1="307.53" x2="391.69" y2="307.53" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-11" x1="289.4" y1="250.27" x2="401.85" y2="250.27" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-12" x1="355.37" y1="300.96" x2="433.72" y2="300.96" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-13" x1="353.59" y1="204.81" x2="500" y2="204.81" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-14" x1="354.72" y1="301.86" x2="498.74" y2="301.86" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-15" x1="96.99" y1="249.36" x2="211.05" y2="249.36" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-16" x1="66.52" y1="305.27" x2="134.77" y2="305.27" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-17" x1="32.47" y1="248.01" x2="144.92" y2="248.01" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-18" x1="98.45" y1="298.7" x2="176.8" y2="298.7" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-19" x1="96.67" y1="202.55" x2="243.08" y2="202.55" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-20" x1="180.08" y1="380.54" x2="323.31" y2="380.54" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-21" x1="129.95" y1="331.85" x2="272.3" y2="331.85" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-22" x1="226.57" y1="331.85" x2="368.92" y2="331.85" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-23" x1="129.17" y1="427.79" x2="273.19" y2="427.79" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-24" x1="0" y1="297.56" x2="146.05" y2="297.56" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-25" x1="1.35" y1="201.08" x2="144.66" y2="201.08" xlink:href="#linear-gradient"/>
    <linearGradient id="linear-gradient-26" x1="226.06" y1="428.41" x2="370.11" y2="428.41" xlink:href="#linear-gradient"/>
  </defs>
  <polygon class="cls-9" points="152.82 142.5 129.22 117.82 243.32 5.58 266.92 30.27 152.82 142.5"/>
  <polygon class="cls-8" points="298.24 452.85 321.84 428.16 200.96 309.25 177.36 333.94 298.24 452.85"/>
  <polygon class="cls-7" points="315.32 176.39 338.92 151.71 248.47 62.72 224.87 87.41 315.32 176.39"/>
  <polygon class="cls-6" points="217.99 209.77 194.39 185.09 239.04 141.16 262.64 165.85 217.99 209.77"/>
  <polygon class="cls-3" points="183.94 174.25 160.35 149.57 249.2 62.16 272.8 86.85 183.94 174.25"/>
  <polygon class="cls-4" points="281.07 208.17 304.67 183.49 249.92 129.63 226.32 154.31 281.07 208.17"/>
  <polygon class="cls-2" points="347.35 145.49 370.95 120.81 248.14 0 224.54 24.69 347.35 145.49"/>
  <polygon class="cls-1" points="281.87 274.56 258.27 249.88 372.37 137.64 395.97 162.33 281.87 274.56"/>
  <polygon class="cls-5" points="444.37 308.46 467.97 283.77 377.52 194.79 353.92 219.47 444.37 308.46"/>
  <polygon class="cls-10" points="347.04 341.83 323.44 317.15 368.09 273.23 391.69 297.91 347.04 341.83"/>
  <polygon class="cls-11" points="313 306.32 289.4 281.63 378.25 194.23 401.85 218.91 313 306.32"/>
  <polygon class="cls-12" points="410.12 340.23 433.72 315.55 378.97 261.69 355.37 286.37 410.12 340.23"/>
  <polygon class="cls-13" points="476.4 277.56 500 252.87 377.19 132.06 353.59 156.75 476.4 277.56"/>
  <polygon class="cls-15" points="378.32 373.43 354.72 348.75 475.14 230.29 498.74 254.98 378.32 373.43"/>
  <polygon class="cls-21" points="187.45 306.19 211.05 281.51 120.59 192.52 96.99 217.21 187.45 306.19"/>
  <polygon class="cls-22" points="90.12 339.57 66.52 314.88 111.17 270.96 134.77 295.65 90.12 339.57"/>
  <polygon class="cls-23" points="56.07 304.05 32.47 279.37 121.32 191.96 144.92 216.65 56.07 304.05"/>
  <polygon class="cls-24" points="153.2 337.97 176.8 313.28 122.04 259.42 98.45 284.11 153.2 337.97"/>
  <polygon class="cls-14" points="219.48 275.29 243.08 250.6 120.27 129.8 96.67 154.49 219.48 275.29"/>
  <polygon class="cls-17" points="203.68 451.72 180.08 427.03 299.71 309.35 323.31 334.04 203.68 451.72"/>
  <polygon class="cls-16" points="272.3 283.97 248.33 259.65 129.95 379.74 153.92 404.06 272.3 283.97"/>
  <polygon class="cls-18" points="226.57 283.97 250.54 259.65 368.92 379.74 344.95 404.06 226.57 283.97"/>
  <polygon class="cls-19" points="249.59 499.36 273.19 474.68 152.77 356.21 129.17 380.9 249.59 499.36"/>
  <polygon class="cls-25" points="122.45 370.13 146.05 345.45 23.6 224.99 0 249.68 122.45 370.13"/>
  <polygon class="cls-20" points="24.95 272.3 1.35 247.61 121.06 129.86 144.66 154.54 24.95 272.3"/>
  <polygon class="cls-26" points="249.66 500 226.06 475.31 346.51 356.83 370.11 381.51 249.66 500"/>
</svg>
        <p class="text-emerald-500 font-black">SiBanksa</p>
        <small class="text-black">Sistem Informasi Bank Sampah</small>
        <div class="splash-dots">
        <span></span>
        <span></span>
        <span></span>
    </div>
    </div>


    @inertia @include('link.bodylink')

    <script>

          document.addEventListener('DOMContentLoaded', () => {
    // Hanya jalankan logika splash jika layar kurang dari 768px (Mobile/Tablet)
    if (window.innerWidth < 768) {
        setTimeout(() => {
            const splash = document.getElementById('splash-native');
            const app = document.getElementById('app')
            if (splash) {
                splash.classList.add('hide');
                setTimeout(() => splash.remove(), 500);
            }
             if (app) {
                setTimeout(() => app.classList.add('ready'), 500)
            }
        }, 2000);
    } else {
        // Jika di desktop, langsung hilangkan saja
        const splash = document.getElementById('splash-native');
        if (splash) splash.style.display = 'none';
         if (app) {
               app.classList.add('ready')
            }
    }
});

     const isDark = localStorage.getItem('darkMode') === 'true' ||
        (localStorage.getItem('darkMode') === null &&
         window.matchMedia('(prefers-color-scheme: dark)').matches)

    if (isDark) {
        document.getElementById('splash-native').style.background = '#111827'
    }

        function themeData() {
            return {
                darkMode: false,
                initTheme() {
                    const saved = localStorage.getItem('darkMode');

                    if (saved !== null) {

                        this.darkMode = saved === 'true';
                    } else {

                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    this.updateHtml();


                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                        if (localStorage.getItem('darkMode') === null) {
                            this.darkMode = e.matches;
                            this.updateHtml();
                        }
                    });

                    this.$watch('darkMode', value => {
                        localStorage.setItem('darkMode', value);
                        this.updateHtml();
                    });
                },
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                },
                updateHtml() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>

</html>
