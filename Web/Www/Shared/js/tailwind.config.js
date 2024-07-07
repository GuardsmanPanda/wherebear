const defaultTheme = require('tailwindcss/defaultTheme')
module.exports = {
    content: ['Web/Www/**/*.blade.php', 'vendor/guardsmanpanda/larabear/src/Web/Www/**/*.php'],
    theme: {
        extend: {
            colors: {
                pistachio: {
                    100: '#F1F8E9',
                    200: '#DCEDC8',
                    300: '#AED581',
                    400: '#9CCC65',
                    500: '#8BC34A',
                    600: '#7CB342',
                    700: '#689F38',
                    800: '#558B2F',
                    900: '#33691E',
                },
            },
            scale: {
                '102': '1.02',
            },
            spacing: {
                '144': '36rem',
                '192': '48rem',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
