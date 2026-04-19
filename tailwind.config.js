const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './resources/views/filament/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.teal,
                success: colors.green,
                warning: colors.yellow,
                'teal-300': '#6ee7b7'
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
