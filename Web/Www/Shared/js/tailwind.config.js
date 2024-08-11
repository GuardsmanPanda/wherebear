const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: ['Web/Www/**/*.blade.php', 'Web/Www/Shared/Component/**/*.php', 'vendor/guardsmanpanda/larabear/src/Web/Www/**/*.php'], theme: {
    fontFamily: {
      body: 'Roboto', heading: 'Outfit',
    }, extend: {
      screens: {
        sm: '460px',
      }, colors: {
        primary: {
          surface: {
            subtle: "#aed581", light: "#9ccc65", default: "#8bc34a", dark: "#7cb342"
          }, border: {
            subtle: "#7cb342", light: "#689f38", default: "#689f38", dark: "#558b2f"
          }, text: "#33691e"
        }, info: {
          surface: {
            subtle: "#badbe7", light: "#97c8db", default: "#4aa4c2", dark: "#35798f"
          }, border: {
            subtle: "#73b6ce", light: "#4aa4c2", default: "#35798f", dark: "#205060"
          }, text: "#35798f"
        }, secondary: {
          text: "#81365b", surface: {
            subtle: "#e7c5d6", light: "#dba9c1", default: "#c47299", dark: "#b04a7b"
          }, border: {
            subtle: "#d090af", light: "#c47299", default: "#81365b", dark: "#56243c"
          }
        }, tertiary: {
          text: "#f97316", surface: {
            subtle: "#fff7ed", light: "#ffedd5", default: "#fed7aa", dark: "#fdba74"
          }, border: {
            subtle: "#fdba74", light: "#fb923c", default: "#f97316", dark: "#ea580c"
          }
        }, success: {
          text: "#5fa0c9", surface: {
            subtle: "#a0c6df", light: "#80b3d4", default: "#5fa0c9", dark: "#447695"
          }, border: {
            subtle: "#a0c6df", light: "#80b3d4", default: "#152936", dark: "#03090f"
          }
        }, error: {
          surface: {
            subtle: "#ecb8c0", light: "#e094a2", default: "#c24a69", dark: "#8f354c"
          }, border: {
            subtle: "#d27085", light: "#c24a69", default: "#8f354c", dark: "#602031"
          }, text: "#8f354c"
        }, warning: {
          text: "#c2410c", surface: {
            subtle: "#fed7aa", light: "#fdba74", default: "#f97316", dark: "#ea580c"
          }, border: {
            subtle: "#fb923c", light: "#f97316", default: "#c2410c", dark: "#9a3412"
          }
        }, shade: {
          surface: {
            subtle: "#d7ccc8", light: "#a1887f", default: "#795548", dark: "#5d4037", disabled: "#efebe9"
          }, border: {
            subtle: "#efebe9", light: "#d7ccc8", default: "#bcaaa4", dark: "#3e2723"
          }, text: {
            title: "#311e1b", subtitle: "#4e342e", body: "#6d4c41", disabled: "#a1887f", negative: "#efebe9"
          }
        }, reward: {
          surface: {
            subtle: "#fef08a", light: "#fde047", default: "#facc15", dark: "#eab308"
          }, border: {
            subtle: "#fef08a", light: "#fde047", default: "#facc15", dark: "#eab308"
          }, text: "#eab308"
        }
      },
    },
  }, plugins: [require('@tailwindcss/forms'),],
}
