/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff9e6',
                    100: '#ffefbf',
                    200: '#ffe08a',
                    300: '#ffd156',
                    400: '#ffc43a',
                    500: '#FFC900',
                    600: '#FF9B00',
                    700: '#d97d00',
                    800: '#a85f00',
                    900: '#744100',
                },
                surface: '#ffffff',
                page: '#FFFDF7',
                panel: '#F7F2E9',
                line: {
                    DEFAULT: '#EDE4D3',
                    strong: '#E2D6BF',
                },
                ink: '#0F172A',
                muted: '#5B6472',
                contrast: {
                    800: '#071a2e',
                    900: '#04111f',
                },
                pickup: '#10B981',
                dropoff: '#EF4444',
                info: '#2563EB',
                warn: '#F59E0B',

                // Compatibility aliases for large Blade templates that still
                // carry primary/accent/navy utility names while rendered
                // through the new Vite design system.
                primary: {
                    50: '#fff9e6',
                    100: '#ffefbf',
                    200: '#ffe08a',
                    300: '#ffd156',
                    400: '#ffc43a',
                    500: '#FFC900',
                    600: '#FF9B00',
                    700: '#d97d00',
                    800: '#a85f00',
                    900: '#744100',
                },
                accent: {
                    DEFAULT: '#FFE100',
                    50: '#fffce5',
                    100: '#fff5b8',
                    500: '#FFE100',
                    600: '#FFC900',
                    700: '#b86100',
                },
                navy: {
                    50: '#eef6ff',
                    100: '#d9ebff',
                    700: '#0f2a44',
                    800: '#071a2e',
                    900: '#04111f',
                    950: '#020915',
                },
                pastel: '#EBE389',
            },
            fontFamily: {
                sans: ['Be Vietnam Pro', 'system-ui', 'sans-serif'],
                display: ['Be Vietnam Pro', 'system-ui', 'sans-serif'],
                header: ['Manrope', 'Be Vietnam Pro', 'system-ui', 'sans-serif'],
            },
            borderRadius: {
                none: '0px',
                sm: '2px',
                DEFAULT: '2px',
                control: '2px',
                panel: '2px',
                pill: '999px',
            },
            boxShadow: {
                soft: 'none',
                card: '0 8px 24px -12px rgba(4, 17, 31, 0.20)',
                lift: 'none',
                none: 'none',
            },
            zIndex: {
                elevated: '10',
                search: '20',
                header: '40',
                'header-menu': '45',
                drawer: '60',
                modal: '70',
                alert: '80',
            },
            transitionTimingFunction: {
                'out-soft': 'cubic-bezier(0.2, 0.8, 0.2, 1)',
            },
            transitionDuration: {
                fast: '150ms',
                base: '250ms',
                slow: '450ms',
            },
            keyframes: {
                'reveal-up': {
                    from: { opacity: '0', transform: 'translateY(4px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'reveal-up': 'reveal-up 200ms ease-out both',
            },
        },
    },
    plugins: [],
};
