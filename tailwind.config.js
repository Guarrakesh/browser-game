/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        fontFamily: {
            'display': [
                "ProFontWindows", "ui-sans-serif", "system-ui",
            ],
            'sans': ['Space Grotesk Variable', 'ui-sans-serif', 'system-ui']
        },
        extend: {},
    },
    plugins: [],
}
