const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    'Web/Www/**/*.blade.php',
    'Web/Www/**/*.lit-component.js',
    'Web/Www/Shared/Component/**/*.php',
    'vendor/guardsmanpanda/larabear/src/Web/Www/**/*.php'
  ], theme: {
    fontFamily: {
      body: 'Roboto',
      heading: 'Baloo'
    }, extend: {
      screens: {
        sm: '460px',
      }, colors: {
        primary: {
          surface: {
            subtle: "#aed581",
            light: "#9ccc65",
            default: "#8bc34a",
            dark: "#7cb342"
          },
          border: {
            subtle: "#7cb342",
            light: "#689f38",
            default: "#689f38",
            dark: "#558b2f"
          },
          text: "#33691e"
        },
        info: {
          surface: {
            subtle: "#badbe7",
            light: "#97c8db",
            default: "#4aa4c2",
            dark: "#35798f"
          },
          border: {
            subtle: "#73b6ce",
            light: "#4aa4c2",
            default: "#35798f",
            dark: "#205060"
          },
          text: "#35798f"
        },
        secondary: {
          text: "#2A3C7A",
          surface: {
            subtle: "#D1D8EF",
            light: "#B7C2E6",
            default: "#95A5DB",
            dark: "#6D83CD"
          },
          border: {
            subtle: "#95a5db",
            light: "#6d83cd",
            default: "#4561bf",
            dark: "#3953a7"
          }
        },
        tertiary: {
          text: "#f6cca7",
          surface: {
            subtle: "#fefaf6",
            light: "#fef5ed",
            default: "#fcebdc",
            dark: "#fae1ca",
            darker: "#f8d6b9"
          },
          border: {
            subtle: "#fdba74",
            light: "#fb923c",
            default: "#f97316",
            dark: "#ea580c"
          }
        },
        success: {
          text: "#447695",
          surface: {
            subtle: "#c0d9ea",
            light: "#a0c6df",
            default: "#5fa0c9",
            dark: "#447695"
          },
          border: {
            subtle: "#80b3d4",
            light: "#5fa0c9",
            default: "#447695",
            dark: "#2c4e64"
          }
        },
        error: {
          surface: {
            subtle: "#ecb8c0",
            light: "#e094a2",
            default: "#c24a69",
            dark: "#8f354c"
          },
          border: {
            subtle: "#d27085",
            light: "#c24a69",
            default: "#8f354c",
            dark: "#602031"
          },
          text: "#8f354c"
        },
        warning: {
          text: "#c2410c",
          surface: {
            subtle: "#fed7aa",
            light: "#fdba74",
            default: "#f97316",
            dark: "#ea580c"
          },
          border: {
            subtle: "#fb923c",
            light: "#f97316",
            default: "#c2410c",
            dark: "#9a3412"
          }
        },
        shade: {
          surface: {
            subtle: "#d7ccc8",
            light: "#a1887f",
            default: "#795548",
            dark: "#5d4037",
            disabled: "#efebe9"
          },
          border: {
            subtle: "#efebe9",
            light: "#d7ccc8",
            default: "#bcaaa4",
            dark: "#5d4037"
          },
          text: {
            title: "#311e1b",
            subtitle: "#4e342e",
            body: "#6d4c41",
            disabled: "#a1887f",
            negative: "#fcfdfc"
          }
        },
        reward: {
          surface: {
            subtle: "#fef08a",
            light: "#fde047",
            default: "#facc15",
            dark: "#eab308"
          },
          border: {
            subtle: "#fef08a",
            light: "#fde047",
            default: "#facc15",
            dark: "#eab308"
          },
          text: "#eab308"
        }

      },
    },
  }, plugins: [
    require('@tailwindcss/forms'),
    function ({ addUtilities, theme }) {
      const strokeWidths = [1, 2, 3];
      const strokeWidthClasses = {
        '.text-stroke': {
          '-webkit-text-stroke-width': '1px',
          '-webkit-text-stroke-color': 'black',
          'paint-order': 'stroke fill',
        },
        ...strokeWidths.reduce((acc, width) => {
          acc[`.text-stroke-${width}`] = {
            '-webkit-text-stroke-width': `${width}px`,
            '-webkit-text-stroke-color': 'black',
            'paint-order': 'stroke fill',
          };
          return acc;
        }, {})
      };

      // Generate text stroke color utilities based on the Tailwind color palette
      const strokeColorClasses = Object.entries(theme('colors')).reduce((acc, [colorName, colorValue]) => {
        if (typeof colorValue === 'string') {
          // If the color value is a string (e.g., "black", "#ffffff")
          acc[`.text-stroke-${colorName}`] = {
            '-webkit-text-stroke-color': colorValue,
          };
        } else {
          // If the color value is an object with shades (e.g., "blue-400")
          Object.entries(colorValue).forEach(([shade, value]) => {
            acc[`.text-stroke-${colorName}-${shade}`] = {
              '-webkit-text-stroke-color': value,
            };
          });
        }
        return acc;
      }, {});

      addUtilities(
        {
          ...strokeWidthClasses,
          ...strokeColorClasses,
        },
        ['responsive', 'hover']
      );
    },
  ],
}
