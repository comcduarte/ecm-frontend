document.addEventListener("DOMContentLoaded", async () => {
    document.querySelectorAll("pre.mermaid > code").forEach((element) => {
        element.parentElement.textContent = element.textContent;
    });

    mermaid.initialize({
        startOnLoad: false
    });

    await mermaid.run({
        querySelector: "pre.mermaid"
    });
});