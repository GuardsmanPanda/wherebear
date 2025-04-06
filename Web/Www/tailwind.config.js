module.exports = {
  plugins: [
    function ({ addUtilities, theme }) {
      const strokeWidths = [1, 2, 3]
      const strokeWidthClasses = {
        ".text-stroke": {
          "-webkit-text-stroke-width": "1px",
          "-webkit-text-stroke-color": "black",
          "paint-order": "stroke fill",
        },
        ...strokeWidths.reduce((acc, width) => {
          acc[`.text-stroke-${width}`] = {
            "-webkit-text-stroke-width": `${width}px`,
            "-webkit-text-stroke-color": "black",
            "paint-order": "stroke fill",
          }
          return acc
        }, {}),
      }

      // Generate text stroke color utilities based on the Tailwind color palette
      const strokeColorClasses = Object.entries(theme("colors")).reduce((acc, [colorName, colorValue]) => {
        if (typeof colorValue === "string") {
          // If the color value is a string (e.g., "black", "#ffffff")
          acc[`.text-stroke-${colorName}`] = {
            "-webkit-text-stroke-color": colorValue,
          }
        } else {
          // If the color value is an object with shades (e.g., "blue-400")
          Object.entries(colorValue).forEach(([shade, value]) => {
            acc[`.text-stroke-${colorName}-${shade}`] = {
              "-webkit-text-stroke-color": value,
            }
          })
        }
        return acc
      }, {})

      addUtilities(
        {
          ...strokeWidthClasses,
          ...strokeColorClasses,
        },
        ["responsive", "hover"],
      )
    },
  ],
}
