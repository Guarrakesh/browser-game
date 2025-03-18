const colors = require('tailwindcss/colors')
const customColors = {
    ...colors, ...{
        slate: {
            50: '#e6e9ea',
            100: '#b0bbbe',
            200: '#8a9b9e',
            300: '#546d72',
            400: '#335157',
            500: '#00252d',
            600: '#002229',
            700: '#001a20',
            800: '#001419',
            900: '#001013'
        },
        surf: {
            50: '#f7fbfa',
            100: '#e6f4ee',
            200: '#d9eee6',
            300: '#c8e6da',
            400: '#bde1d3',
            500: '#addac8',
            600: '#9dc6b6',
            700: '#7b9b8e',
            800: '#5f786e',
            900: '#495c54'
        },
        sepia: {
            50: '#f5efec',
            100: '#e0ccc4',
            200: '#d1b4a7',
            300: '#bd917f',
            400: '#b07c66',
            500: '#9c5b40',
            600: '#8e533a',
            700: '#6f412d',
            800: '#563223',
            900: '#42261b'

        },
        orange: {
            50: '#f9f2ec',
            100: '#ebd8c4',
            200: '#e1c5a8',
            300: '#d3ab80',
            400: '#cb9a68',
            500: '#be8142',
            600: '#ad753c',
            700: '#875c2f',
            800: '#694724',
            900: '#50361c'
        },
        green: {
            50: '#e8f3ed',
            100: '#b8d8c7',
            200: '#96c6ac',
            300: '#66ab86',
            400: '#489b6f',
            500: '#1a824b',
            600: '#187644',
            700: '#125c35',
            800: '#0e4829',
            900: '#0b3720'
        },
        'matisse': {
            '50': '#f5f7fa',
            '100': '#e9eef5',
            '200': '#cedce9',
            '300': '#a3bdd6',
            '400': '#729abe',
            '500': '#507ea7',
            '600': '#3c6289',
            '700': '#325072',
            '800': '#2d465f',
            '900': '#293b51',
            '950': '#1c2735',
        },
    }
}

/** @type {import('tailwindcss').Config} */

const globalColors = {
    brand: customColors.sepia,
    neutral: customColors.slate,
    darkNeutral: customColors.slate,
    error: customColors.red,
    warning: customColors.orange,
    success: customColors.green
}
module.exports = {
    content: [
        "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    darkMode: 'selector',
    theme: {
        fontFamily: {
            'display': [
                "ProFontWindows", "ui-sans-serif", "system-ui",
            ],
            'sans': ['Space Grotesk Variable', 'ui-sans-serif', 'system-ui']
        },
        extend: {
            colors: {...colors, ...customColors},
            backgroundColor: {
                "brand-primary": globalColors.brand[700],
                "brand-primary-hover": globalColors.brand[500],
                "brand-secondary": globalColors.brand[200],
                "brand-secondary-hover": globalColors.brand[100],
                "neutral-primary": globalColors.neutral[800],
                "neutral-secondary": globalColors.neutral[900],
                "neutral-tertiary": globalColors.neutral[950],
                "darkneutral-primary": globalColors.darkNeutral[500],
                "darkneutral-primary-hover": globalColors.darkNeutral[600],
                "darkneutral-secondary": globalColors.darkNeutral[400],
                "darkneutral-secondary-hover": globalColors.darkNeutral[300],
                "error-primary": globalColors.error[500],
                "error-secondary": globalColors.error[800],
                "warning-primary": globalColors.warning[500],
                "warning-secondary": globalColors.warning[800],
                "success-primary": globalColors.success[500],
                "success-secondary": globalColors.success[800],
            },
            textColor: {
                "brand-onprimary": globalColors.neutral[50],
                "brand-onsecondary": globalColors.darkNeutral[500],
                "darkneutral-onprimary": globalColors.neutral[50],
                "darkneutral-onsecondary": globalColors.neutral[100],
                "neutral-onprimary": globalColors.darkNeutral[500],
                "neutral-onsecondary": globalColors.darkNeutral[800],
                'neutral-primary': globalColors.darkNeutral[500],
                'neutral-secondary': globalColors.darkNeutral[400],
                "neutral-emphasis": globalColors.brand[900],
                "error-primary": globalColors.error[500],
                "error-secondary": globalColors.error[800],
                "error-onprimary": globalColors.neutral[50],
                "error-onsecondary": globalColors.neutral[50],
                "success-onprimary": globalColors.neutral[50],
                "success-onsecondary": globalColors.neutral[50],
                "warning-onprimary": globalColors.neutral[50],
                "warning-onsecondary": globalColors.neutral[50],



            },
            ringColor: {
                "brand-primary": globalColors.brand[500],
                "brand-secondary": globalColors.brand[300],
                "neutral-primary": globalColors.darkNeutral[800],
                "neutral-secondary": globalColors.darkNeutral[500]
            }
        },
    },
    plugins: [],
}
