<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    @routes
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
    @include('link.headlink')
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
    @inertia @include('link.bodylink')

    <script>
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
