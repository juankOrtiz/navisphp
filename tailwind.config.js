/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./views/**/*.{php,html}",
        "./public/js/**/*.js",
    ],
    theme: {},
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
