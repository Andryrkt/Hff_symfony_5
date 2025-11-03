// assets/types/global.d.ts

// Déclarations globales TypeScript
declare const require: {
    context: (path: string, deep?: boolean, filter?: RegExp) => {
        keys: () => string[];
        (id: string): any;
    };
} | undefined;

declare interface Window {
    appInstance: any;
    $: any;
    Turbo: any;
}

// Déclarations pour les modules
declare module '*.scss' {
    const content: any;
    export default content;
}

declare module '*.css' {
    const content: any;
    export default content;
}