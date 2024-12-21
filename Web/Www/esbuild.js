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

function buildJs({ name, entryPoint, outputFileName }) {
  logStartingBuild(`Starting building ${name} JS...`);
  const storagePath = 'storage/app';

  try {
    // Ensure the storage directory exists
    if (!fs.existsSync(storagePath)) {
      fs.mkdirSync(storagePath, { recursive: true });
    }

    // Build the JavaScript file using esbuild
    esbuild.buildSync({
      entryPoints: [entryPoint],
      entryNames: '[name]',
      sourcemap: true,
      bundle: true,
      minify: true,
      logLevel: "info",
      define: { global: "window" },
      outdir: 'public/static/dist',
    });

    // Write the generated file path with a unique ID to storage
    fs.writeFileSync(
      `${storagePath}/${outputFileName}-js-path.txt`,
      `/static/dist/${outputFileName}.js?id=${createUniqId()}`
    );

    logCompletedBuild(`${name} JS build completed`);
  } catch (error) {
    console.error(`Error building ${name} JS:`, error);
    process.exit(1);
  }
}

function buildCss({ name, entryPoint, outputFileName, configFilePath }) {
  logStartingBuild(`Starting building ${name} CSS...`);
  const storagePath = 'storage/app';

  try {
    // Ensure the storage directory exists
    if (!fs.existsSync(storagePath)) {
      fs.mkdirSync(storagePath, { recursive: true });
    }

    execSync(`npx tailwindcss ${configFilePath ? `-c ${configFilePath}` : ''} -i ${entryPoint} -o public/static/dist/${outputFileName}.css --minify`);
    fs.writeFileSync(`${storagePath}/${outputFileName}-css-path.txt`, `/static/dist/${outputFileName}.css?id=${createUniqId()}`);
    logCompletedBuild(`${name} CSS build completed`);
  } catch (error) {
    console.error(`Error building ${name} JS:`, error);
    process.exit(1);
  }
}

function buildCssForLitComponent({ name, inputFilePath, outputFileName, exportName }) {
  logStartingBuild(`Starting building ${name} CSS for Lit components...`);
  const outputFilePath = `public/static/dist/lit-${outputFileName}-css.js`;

  try {
    const cssContent = fs.readFileSync(inputFilePath, 'utf8');
    let cleanCssContent = cssContent.replaceAll("`", "").replaceAll("\\", "\\\\");

    const content = `
    import { css } from "lit";
    export const ${exportName} = css\`${cleanCssContent}\`;
    `;

    fs.writeFileSync(outputFilePath, content, 'utf8');
    logCompletedBuild(`${name} CSS for Lit components build completed`);
  } catch (error) {
    console.error(`Error building ${name} CSS for Lit components:`, error);
    process.exit(1);
  }
}

function buildLitComponents() {
  logStartingBuild('Starting building Lit components...');
  const distPath = 'public/static/dist/lit-components';
  const storagePath = 'storage/app/lit-components';

  try {
    if (!fs.existsSync(distPath)) {
      fs.mkdirSync(distPath, { recursive: true });
    }
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

function buildLitDirectives() {
  logStartingBuild('Starting building Lit directives...');
  const distPath = 'public/static/dist/lit-directives';
  const storagePath = 'storage/app/lit-directives';

  try {
    if (!fs.existsSync(distPath)) {
      fs.mkdirSync(distPath, { recursive: true });
    }
    if (!fs.existsSync(storagePath)) {
      fs.mkdirSync(storagePath, { recursive: true });
    }

    esbuild.buildSync({
      entryPoints: glob.sync(['Web/Www/**/*.lit-directive.js']),
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
      fs.writeFileSync(`${storagePath}/${name}.txt`, `/static/dist/lit-directives/${name}.js?id=${createUniqId()}`);
    });
    logCompletedBuild('Lit directives build completed');
  } catch (error) {
    console.error('Error building Lit directives:', error);
    process.exit(1);
  }
}

function build() {
  console.log(`${GREEN_COLOR}STARTING BUILD PROCESS...${DEFAULT_COLOR}`);
  buildJs({
    name: 'App',
    entryPoint: 'Web/Www/Shared/js/app.js',
    outputFileName: 'app',
  });
  buildJs({
    name: 'WebSocketService',
    entryPoint: 'Web/Www/Shared/js/websocket.service.js',
    outputFileName: 'websocket.service',
  });
  buildJs({
    name: 'AchievementToastService',
    entryPoint: 'Web/Www/Shared/js/achievement-toast.service.js',
    outputFileName: 'achievement-toast.service',
  });
  buildJs({
    name: 'ToastContainer',
    entryPoint: 'Web/Www/Shared/js/toast/toast-container.js',
    outputFileName: 'toast-container',
  });
  buildJs({
    name: 'AchievementToast',
    entryPoint: 'Web/Www/Shared/js/toast/achievement-toast.js',
    outputFileName: 'achievement-toast',
  });
  buildCss({
    name: 'App',
    entryPoint: 'Web/Www/Shared/css/app.css',
    outputFileName: 'app'
  });
  buildCss({
    name: 'Tailwind',
    entryPoint: 'Web/Www/Shared/css/tailwind.css',
    outputFileName: 'tailwind',
    configFilePath: 'Web/Www/tailwind.config.js'
  });
  buildCssForLitComponent({
    name: 'App',
    inputFilePath: 'public/static/dist/app.css',
    outputFileName: 'app',
    exportName: 'AppStyles'
  });
  buildCssForLitComponent({
    name: 'Tailwind',
    inputFilePath: 'public/static/dist/tailwind.css',
    outputFileName: 'tailwind',
    exportName: 'TailwindStyles'
  })
  buildLitComponents();
  buildLitDirectives();
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
