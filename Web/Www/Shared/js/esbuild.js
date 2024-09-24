const esbuild = require('esbuild');
const exec = require('child_process').exec;
const fs = require('fs');
const glob = require('glob');
const util = require('util');

const execPromise = util.promisify(exec);
const mkdirPromise = util.promisify(fs.mkdir);
const readdirPromise = util.promisify(fs.readdir);
const writeFilePromise = util.promisify(fs.writeFile);
const readFilePromise = util.promisify(fs.readFile);

function createUniqId() {
  return `${Math.random().toString(36).substring(2)}${Math.random().toString(36).substring(2)}`;
}

async function buildApp() {
  try {
    await esbuild.build({
      entryPoints: ['Web/Www/Shared/js/app.js'],
      sourcemap: true,
      bundle: true,
      minify: true,
      logLevel: "info",
      define: {
        global: "window"
      },
      outdir: 'public/static/dist',
    });

    await writeFilePromise('storage/app/app-js-path.txt', `/static/dist/app.js?id=${createUniqId()}`);

    await execPromise('npx tailwindcss -i Web/Www/Shared/css/app.css -o public/static/dist/app.css --minify');
    await writeFilePromise('storage/app/app-css-path.txt', `/static/dist/app.css?id=${createUniqId()}`);
    console.log('App build completed\n');
  } catch (error) {
    console.error('Error building app:', error);
    process.exit(1);
  }
}

async function buildTailwindCss() {
  try {
    await execPromise('npx tailwindcss -c Web/Www/Shared/js/tailwind.config.js -i Web/Www/Shared/css/tailwind.css -o public/static/dist/tailwind.css --minify');
    await writeFilePromise(`storage/app/tailwind-css-path.txt`, `/static/dist/tailwind.css?id=${createUniqId()}`);
    console.log('Tailwind CSS build completed\n');
  } catch (error) {
    console.error('Error building CSS:', error);
    process.exit(1);
  }
}

/** 
 * Generates a JavaScript file containing Tailwind CSS classes, which
 * can be imported and used in Lit components for applying Tailwind styles.
 */
async function buildTailwindCssForLitComponents() {
  const inputCssFilePath = 'public/static/dist/tailwind.css';
  const outputJsFilePath = 'public/static/dist/lit-tailwind-css.js';

  try {
    const cssContent = await readFilePromise(inputCssFilePath, 'utf8');

    let cleanCssContent = cssContent.replaceAll("`", "").replaceAll("\\", "\\\\");

    const content = `
    import { css } from "lit";
    export const TailwindStyles = css\`${cleanCssContent}\`
    `;

    await writeFilePromise(outputJsFilePath, content, 'utf8');
    console.log('Tailwind CSS for Lit components build completed');
  } catch (error) {
    console.error('Error building Lit Tailwind:', error);
    process.exit(1);
  }
}

async function buildLitComponents() {
  const distPath = 'public/static/dist/lit-components';
  const storagePath = 'storage/app/lit-components';

  try {
    await esbuild.build({
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
    });

    await mkdirPromise(distPath, { recursive: true });
    const files = await readdirPromise(distPath);
    await mkdirPromise(storagePath, { recursive: true });

    for (const file of files) {
      const name = file.split('.js')[0];
      await writeFilePromise(`${storagePath}/${name}.txt`, `/static/dist/lit-components/${name}.js?id=${createUniqId()}`);
    }

    console.log('Lit components build completed\n');
  } catch (error) {
    console.error('Error building lit components:', error);
    process.exit(1);
  }
}

async function build() {
  console.log('Starting build process...');
  await buildApp();
  await buildTailwindCss();
  await buildTailwindCssForLitComponents();
  await buildLitComponents();
  console.log('Build process completed.');
}

function debounce(fn, delay) {
  let timeoutId;
  return (...args) => {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }
    timeoutId = setTimeout(() => fn(...args), delay);
  };
}

build()
  .then(() => {
    if (process.argv[2] === 'watch') {
      const paths = ['Web/Www'];

      const pwd = process.env.PWD;
      console.log('Current directory: ' + pwd);

      let watchedPathsOutput = 'Watching for changes in:\n';
      paths.forEach(path => {
        watchedPathsOutput += `    - ${path}\n`;
      });
      console.log(watchedPathsOutput);

      const envFile = fs.readFileSync(pwd + '/.env', 'utf8');
      const appUrl = envFile.match(/APP_URL=(.*)/)[1];
      console.log('App URL: ' + appUrl);

      /** Debounces the build function to avoid multiple triggers. */
      const debouncedBuild = debounce(() => {
        console.log('Rebuilding due to file change...');
        build();
      }, 300);

      paths.forEach(path => {
        fs.watch(path, { recursive: true }, (eventType, filename) => {
          console.log('File changed: ' + filename);
          fetch(appUrl + '/system/reload')
            .then(response => {
              if (response.status > 299) {
                console.log('Looks like there was a problem with frontend reload. Status Code: ' + response.status);
              }
            })
            .catch(error => {
              console.error('Fetch error:', error);
            });
          debouncedBuild();
        });
      });
    }
  });