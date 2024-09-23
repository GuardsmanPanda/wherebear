const esbuild = require('esbuild');
const exec = require('child_process').exec;
const glob = require('glob');
const fs = require('fs');


function createUniqId() {
  return `${Math.random().toString(36).substring(2)}${Math.random().toString(36).substring(2)}`;
}

function buildApp() {
  esbuild.build({
    entryPoints: ['Web/Www/Shared/js/app.js'],
    sourcemap: true,
    bundle: true,
    minify: true,
    logLevel: "info",
    define: {
      global: "window"
    },
    outdir: 'public/static/dist',
  }).catch(() => process.exit(1));


  exec('npx tailwindcss -c Web/Www/Shared/js/tailwind.config.js -i Web/Www/Shared/css/app.css -o public/static/dist/app.css', function (error, stdout, stderr) {
    console.log('\n----\nCSS ' + stdout);
    console.log(stderr);
  });


  fs.writeFile('storage/app/app-css-path.txt', `/static/dist/app.css?id=${createUniqId()}`, (err) => {
    if (err) throw err;
  });

  fs.writeFile('storage/app/app-js-path.txt', `/static/dist/app.js?id=${createUniqId()}`, (err) => {
    if (err) throw err;
  });
}

function buildLitComponents() {
  const distPath = 'public/static/dist/lit-components';
  const storagePath = 'storage/app/lit-components';

  esbuild.build({
    entryPoints: glob.sync(['Web/Www/**/*.lit-component.js']),
    entryNames: '[name]',
    sourcemap: true,
    bundle: true,
    minify: true,
    logLevel: "info",
    define: {
      global: "window"
    },
    outdir: distPath,
  })
    .then(() => {
      fs.mkdir(distPath, { recursive: true }, (err) => {
        if (err) throw err;

        fs.readdir(distPath, (err, files) => {
          if (err) throw err;

          fs.mkdir(storagePath, { recursive: true }, (err) => {
            if (err) throw err;

            files.forEach(file => {
              const name = file.split('.js')[0];
              fs.writeFile(`${storagePath}/${name}.txt`, `/static/dist/lit-components/${name}.js?id=${createUniqId()}`, err => {
                if (err) throw err;
              });
            });
          });
        });
      });
    })
    .catch(() => process.exit(1));
}

function build() {
  buildApp();
  buildLitComponents();
}

build();
console.log('Build complete');

if (process.argv[2] === 'watch') {
  const paths = ['Web/Www']

  const pwd = process.env.PWD
  console.log('Current directory: ' + pwd)

  let watchedPathsOutput = 'Watching for changes in:\n';
  paths.forEach(path => {
    watchedPathsOutput += `    - ${path}\n`;
  });
  console.log(watchedPathsOutput)


  const envFile = fs.readFileSync(pwd + '/.env', 'utf8')
  const appUrl = envFile.match(/APP_URL=(.*)/)[1]
  console.log('App URL: ' + appUrl)


  paths.forEach(path => {
    fs.watch(path, { recursive: true }, (eventType, filename) => {
      console.log('File changed: ' + filename);
      fetch(appUrl + '/system/reload').then(response => {
        if (response.status > 299) {
          console.log('Looks like there was a problem with frontend reload. Status Code: ' + response.status);
        }
      }).catch(error => {
        console.error('Fetch error:', error);
      });
      build();
    })
  });
}
