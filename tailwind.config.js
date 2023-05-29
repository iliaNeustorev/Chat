const { colors } = require('laravel-mix/src/Log');
const defaultTheme = require('tailwindcss/defaultTheme');
const plugin = require('tailwindcss/plugin')

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans]
            },
            colors: {
                primary: '#FF4D00',
                accent: '#DB4224',
                accentHover: '#AF351D',
                text1: '#1A1A1A',
                text2: '#555555',
                bg1: '#FFFFFF',
                bg2: '#F2F2F2',
                bg3: '#E2E2E5',
                bg4: '#FAF8F5',
                line: '#E4E4E4',
                disabled: '#CCCCCC',
                focus: '#5555C6',
                icone: '#FFCCBC',
                lightGreen: '#F2FFF0',
                error: '#F00000',
                statusGreen: '#2E7D32',
                statusBlue: '#1976D2',
                statusYellow: '#FFD600'
            },
            borderRadius: {
                DEFAULT: '5px'
            },
            boxShadow: {
                base1: '0px 3px 5px rgba(51, 51, 51, 0.1)',
                base2: '0px 5px 12px rgba(51, 51, 51, 0.25)',
                base3: '0px 4px 8px 3px rgba(0, 0, 0, 0.15), 0px 1px 3px rgba(0, 0, 0, 0.3)',
                active: '0px 0px 0px 2px rgba(219, 66, 36, 0.8)'
            },
            keyframes: {
                show: {
                    '0%': { transform: 'scale(.7)' },
                    '45%': { transform: 'scale(1.05)' },
                    '80%': { transform: 'scale(.95)' },
                    '100%': { transform: 'scale(1)' }
                },
                bell: {
                    '0%': { transform: 'rotateZ(0)' },
                    '1%': { transform: 'rotateZ(30deg)' },
                    '3%': { transform: 'rotateZ(-28deg)' },
                    '5%': { transform: 'rotateZ(34deg)' },
                    '7%': { transform: 'rotateZ(-32deg)' },
                    '9%': { transform: 'rotateZ(30deg)' },
                    '11%': { transform: 'rotateZ(-28deg)' },
                    '13%': { transform: 'rotateZ(26deg)' },
                    '15%': { transform: 'rotateZ(-24deg)' },
                    '17%': { transform: 'rotateZ(22deg)' },
                    '19%': { transform: 'rotateZ(-20deg)' },
                    '21%': { transform: 'rotateZ(18deg)' },
                    '23%': { transform: 'rotateZ(-16deg)' },
                    '25%': { transform: 'rotateZ(14deg)' },
                    '27%': { transform: 'rotateZ(-12deg)' },
                    '29%': { transform: 'rotateZ(10deg)' },
                    '31%': { transform: 'rotateZ(-8deg)' },
                    '33%': { transform: 'rotateZ(6deg)' },
                    '35%': { transform: 'rotateZ(-4deg)' },
                    '37%': { transform: 'rotateZ(2deg)' },
                    '39%': { transform: 'rotateZ(-1deg)' },
                    '41%': { transform: 'rotateZ(1deg)' },
                    '43%': { transform: 'rotateZ(0)' },
                    '100%': { transform: 'rotateZ(0)' }
                }
            },
            transitionProperty: {
                height: 'height'
            }
        }
    },

    plugins: [
        require('@tailwindcss/forms'),
        plugin(function ({ addBase, addComponents, addUtilities, theme }) {
            addComponents({
                '.group-rating:hover div > span': {
                    backgroundColor: theme('colors.accent'),
                    color: theme('colors.white'),
                },
                '.group-rating div:hover ~ div > span': {
                    backgroundColor: theme('colors.white'),
                    color: theme('colors.text1'),
                },
                '.loading': {
                    '&:before': {
                        content: '',
                        display: 'inline-block',
                        width: '16px',
                        height: '16px',
                        borderWidth: '4px',
                        borderRadius: '100%',
                        borderRightColor: 'transparent',
                        marginRight: '8px',
                        animation: 'spin 1s linear infinite'
                    }
                },
                '.btn-primary': {
                    color: theme('colors.white'),
                    fontSize: '17px',
                    fontWeight: 600,
                    borderRadius: theme('borderRadius.DEFAULT'),
                    padding: '10px 20px',
                    backgroundColor: theme('colors.accent'),
                    '&:hover': {
                        backgroundColor: theme('colors.accentHover')
                    }
                },
                '.btn-primary.disabled': {
                    color: theme('colors.disabled'),
                    backgroundColor: theme('colors.line')
                },
                '.btn-secondary': {
                    fontSize: '14px',
                    color: theme('colors.accent'),
                    backgroundColor: theme('colors.bg2'),
                    borderRadius: theme('borderRadius.DEFAULT'),
                    padding: '5px 10px',
                    '&:hover': {
                        color: theme('colors.white'),
                        backgroundColor: theme('colors.accent')
                    }
                },
                '.btn-secondary.disabled': {
                    color: theme('colors.disabled'),
                    backgroundColor: theme('colors.line')
                },
                '.btn-flat': {
                    fontSize: '14px',
                    color: theme('colors.accent'),
                    backgroundColor: 'transparent',
                    '&:hover': {
                        color: theme('colors.accentHover')
                    },
                    '@media (min-width: 1536px)': {
                        fontSize: '17px',
                        fontWeight: 600
                    }
                },
                '.btn-flat.disabled': {
                    color: theme('colors.disabled')
                },
                '.btn-menu': {
                    fontSize: '14px',
                    color: theme('colors.text2'),
                    backgroundColor: theme('colors.white'),
                    borderRadius: theme('borderRadius.DEFAULT'),
                    padding: '10px 15px',
                    boxShadow: theme('boxShadow.base1'),
                    '&:hover': {
                        color: theme('colors.accent'),
                        boxShadow: theme('boxShadow.base2')
                    },
                    '@media (min-width: 1536px)': {
                        fontSize: '20px',
                        padding: '15px 30px'
                    }
                },
                '.btn-menu.active': {
                    color: theme('colors.accent'),
                    boxShadow: theme('boxShadow.active')
                },
                '.btn-round': {
                    fontSize: '12px',
                    color: theme('colors.text2'),
                    backgroundColor: theme('colors.white'),
                    borderRadius: '50px',
                    padding: '10px 15px',
                    borderWidth: '1px',
                    borderStyle: 'solid',
                    borderColor: theme('colors.line'),
                    '&:hover': {
                        backgroundColor: theme('colors.bg2')
                    },
                    '@media (min-width: 1536px)': {
                        fontSize: '14px',
                    }
                },
                '.btn-round.active': {
                    color: theme('colors.white'),
                    borderColor: theme('colors.text2'),
                    backgroundColor: theme('colors.text2')
                },
                '.btn-credit': {
                    fontSize: '17px',
                    color: theme('colors.focus'),
                    fontWeight: 600,
                    backgroundColor: theme('colors.bg3'),
                    borderRadius: '30px',
                    padding: '15px',
                    '&:hover': {
                        backgroundColor: theme('colors.bg2')
                    }
                },
                '.btn-credit.disabled': {
                    color: theme('colors.disabled'),
                    backgroundColor: theme('colors.line')
                }
            });
        })
    ]
};
