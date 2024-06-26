const defaultTheme = require('tailwindcss/defaultTheme')
module.exports = {
    content: ['Web/Www/**/*.blade.php', 'vendor/guardsmanpanda/larabear/src/Web/Www/**/*.php'],
    theme: {
        extend: {
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
