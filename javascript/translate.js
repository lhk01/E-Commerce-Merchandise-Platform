const apiKey = "AIzaSyB5BHGXn6QJwguILe2QCRF1fEkAadGgOMM"; // Replace with your actual API key
let originalTextNodes = []; // To store the original text nodes and their content

// Function to find all text nodes on the page
const getTextNodes = (node) => {
    let textNodes = [];
    if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== "") {
        textNodes.push(node);
    } else if (node.nodeType === Node.ELEMENT_NODE && node.tagName !== "SCRIPT" && node.tagName !== "STYLE") {
        for (let child of node.childNodes) {
            textNodes = textNodes.concat(getTextNodes(child));
        }
    }
    return textNodes;
};

// Function to save the original text
const saveOriginalText = () => {
    const textNodes = getTextNodes(document.body);
    originalTextNodes = textNodes.map(node => ({ node, originalText: node.nodeValue }));
};

// Function to restore original text
const restoreOriginalText = () => {
    originalTextNodes.forEach(({ node, originalText }) => {
        node.nodeValue = originalText;
    });
};

// Function to translate content
const translateContent = async (targetLanguage) => {
    if (targetLanguage === "en") {
        restoreOriginalText(); // Restore original text if English is selected
        return;
    }

    const textNodes = getTextNodes(document.body);
    const textsToTranslate = textNodes.map(node => node.nodeValue);

    try {
        const response = await fetch(`https://translation.googleapis.com/language/translate/v2?key=${apiKey}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                q: textsToTranslate,
                target: targetLanguage,
            }),
        });

        const data = await response.json();
        const translations = data.data.translations;

        // Replace text nodes with their translations
        translations.forEach((translation, index) => {
            textNodes[index].nodeValue = translation.translatedText;
        });
    } catch (error) {
        console.error("Error during translation:", error);
    }
};

// Add event listener to the language selector
document.addEventListener("DOMContentLoaded", () => {
    const languageSelector = document.getElementById("language-selector");

    saveOriginalText(); // Save the original text when the page loads

    // Load saved language preference
    const savedLanguage = localStorage.getItem("preferredLanguage");
    if (savedLanguage && savedLanguage !== "en") {
        languageSelector.value = savedLanguage; // Set the selector to the saved language
        translateContent(savedLanguage); // Apply the saved language
    }

    // Save language preference on change
    if (languageSelector) {
        languageSelector.addEventListener("change", () => {
            const targetLanguage = languageSelector.value;
            localStorage.setItem("preferredLanguage", targetLanguage); // Save the preference
            translateContent(targetLanguage); // Translate content
        });
    }
});

