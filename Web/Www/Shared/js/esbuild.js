const esbuild = require('esbuild');
const exec = require('child_process').exec;
const fs = require('fs')


const build = function () {
    esbuild.build({
        entryPoints: ['Web/Www/Shared/js/app.js'],
        sourcemap: true,
        bundle: true,
        minify: true,
        logLevel: "info",
        define: {
            global: "window"
        },
        outfile: 'public/static/dist/app.js',
    }).catch(() => process.exit(1))

    exec('npx tailwindcss -c Web/Www/Shared/js/tailwind.config.js -i Web/Www/Shared/css/app.css -o public/static/dist/app.css', function(error, stdout, stderr){ console.log('\n----\nCSS ' + stdout);  console.log(stderr); });

    fs.writeFile(
        'storage/app/app-css-path.txt',
        "/static/dist/app.css?id=" + Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2),
        (err) => {
            if (err) throw err;
        });
    fs.writeFile(
        'storage/app/app-js-path.txt',
        "/static/dist/app.js?id=" + Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2),
        (err) => {
            if (err) throw err;
        });
}

build()
console.log('Build complete')

if (process.argv[2] === 'watch') {
    const pwd = process.env.PWD
    console.log('Watching for changes...')
    console.log('Current directory: ' + pwd + ", watching: " + pwd + "/Web/Www")

    const envFile = fs.readFileSync(pwd + '/.env', 'utf8')
    const appUrl = envFile.match(/APP_URL=(.*)/)[1]
    console.log('App URL: ' + appUrl)


    fs.watch('Web/Www', { recursive: true }, (eventType, filename) => {
        console.log('File changed: ' + filename)
        let _ = fetch(appUrl + '/system/reload').then(
            function (response) {
                if (response.status > 299) {
                    console.log('Looks like there was a problem with frontend reload. Status Code: ' + response.status);
                }
            }
        );
        build()
    })
}
