const esbuild = require('esbuild');
const execSync = require('child_process').execSync;
const fs = require('fs');
const glob = require('glob');

const CYAN_COLOR = '\x1b[36m';
const DEFAULT_COLOR = '\x1b[0m';
const GREEN_COLOR = '\x1b[32m';
const YELLOW_COLOR = '\x1b[33m';
const CHECKMARK_ICON = '\u2714';
const ROCKET_ICON = '🚀';

const pwd = process.env.PWD;
const envFile = fs.readFileSync(pwd + '/.env', 'utf8');
const appUrl = envFile.match(/APP_URL=(.*)/)[1];

function logStartingBuild(message) {
  console.log(`\n${YELLOW_COLOR}${message}${DEFAULT_COLOR}`);
}

function logCompletedBuild(message) {
  console.log(`\n${GREEN_COLOR}${message} ${CHECKMARK_ICON}${DEFAULT_COLOR}\n`);
}

function createUniqId() {
  return `${Math.random().toString(36).substring(2)}${Math.random().toString(36).substring(2)}`;
}

function buildApp() {
  logStartingBuild('Starting building app...');
  const storagePath = 'storage/app';

  try {
    if (!fs.existsSync(storagePath)) {
      fs.mkdirSync(storagePath, { recursive: true });
    }

    esbuild.buildSync({
      entryPoints: ['Web/Www/Shared/js/app.js'],
      sourcemap: true,
      bundle: true,
      minify: true,
      logLevel: "info",
      define: { global: "window" },
      outdir: 'public/static/dist',
    });

    fs.writeFileSync(`${storagePath}/app-js-path.txt`, `/static/dist/app.js?id=${createUniqId()}`);
    logCompletedBuild('App build completed');
  } catch (error) {
    console.error('Error building app:', error);
    process.exit(1);
  }
}

function buildTailwindCss() {
  logStartingBuild('Starting building Tailwind CSS...');
  const storagePath = 'storage/app';

  try {
    if (!fs.existsSync(storagePath)) {
      fs.mkdirSync(storagePath, { recursive: true });
    }

    execSync('npx tailwindcss -c Web/Www/tailwind.config.js -i Web/Www/Shared/css/tailwind.css -o public/static/dist/tailwind.css');
    fs.writeFileSync(`${storagePath}/tailwind-css-path.txt`, `/static/dist/tailwind.css?id=${createUniqId()}`);
    logCompletedBuild('Tailwind CSS build completed');
  } catch (error) {
    console.error('Error building Tailwind CSS:', error);
    process.exit(1);
  }
}

function buildTailwindCssForLitComponents() {
  logStartingBuild('Starting building Tailwind CSS for Lit components...');
  const inputCssFilePath = 'public/static/dist/tailwind.css';
  const outputJsFilePath = 'public/static/dist/lit-tailwind-css.js';

  try {
    const cssContent = fs.readFileSync(inputCssFilePath, 'utf8');
    let cleanCssContent = cssContent.replaceAll("`", "").replaceAll("\\", "\\\\");

    const content = `
    import { css } from "lit";
    export const TailwindStyles = css\`${cleanCssContent}\`;
    `;

    fs.writeFileSync(outputJsFilePath, content, 'utf8');
    logCompletedBuild('Tailwind CSS for Lit components build completed');
  } catch (error) {
    console.error('Error building Lit Tailwind:', error);
    process.exit(1);
  }
}

function buildLitComponents() {
  logStartingBuild('Starting building Lit components...');
  const distPath = 'public/static/dist/lit-components';
  const storagePath = 'storage/app/lit-components';

  try {
    if (!fs.existsSync(storagePath)) {
      fs.mkdirSync(storagePath, { recursive: true });
    }

    esbuild.buildSync({
      entryPoints: glob.sync(['Web/Www/**/*.lit-component.js']),
      entryNames: '[name]',
      sourcemap: true,
      bundle: true,
      minify: true,
      logLevel: "info",
      define: { global: "window" },
      outdir: distPath,
    });

    const files = fs.readdirSync(distPath);
    files.forEach(file => {
      const name = file.split('.js')[0];
      fs.writeFileSync(`${storagePath}/${name}.txt`, `/static/dist/lit-components/${name}.js?id=${createUniqId()}`);
    });
    logCompletedBuild('Lit components build completed');
  } catch (error) {
    console.error('Error building Lit components:', error);
    process.exit(1);
  }
}

function build() {
  console.log(`${GREEN_COLOR}STARTING BUILD PROCESS...${DEFAULT_COLOR}`);
  buildApp();
  buildTailwindCss();
  buildTailwindCssForLitComponents();
  buildLitComponents();
  console.log(`${GREEN_COLOR}BUILD PROCESS COMPLETED.${DEFAULT_COLOR} ${ROCKET_ICON}\n`);
  console.log(`${CYAN_COLOR}App URL: ${appUrl}${DEFAULT_COLOR}`);
  console.log(`${CYAN_COLOR}Current directory: ${pwd}${DEFAULT_COLOR}`);
}

build();

if (process.argv[2] === 'watch') {
  const paths = ['Web/Www'];
  const ignoredFilenames = ['esbuild.js'];

  let watchedPathsOutput = '\nWatching for changes in:\n';
  paths.forEach(path => {
    watchedPathsOutput += `    - ${path}\n`;
  });
  console.log(watchedPathsOutput);

  paths.forEach(path => {
    fs.watch(path, { recursive: true }, (eventType, filename) => {
      if (filename && ignoredFilenames.includes(filename)) {
        return;
      }

      console.log('File changed:', filename);
      try {
        execSync(`curl ${appUrl}/system/reload`);
        build();
      } catch (error) {
        console.error('Error triggering system reload:', error);
      }
    });
  });
}
