document.addEventListener("DOMContentLoaded", function () {
    // Find all file inputs on the page
    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach((fileInput) => {
        fileInput.addEventListener("change", async function () {
            const file = fileInput.files[0];
            const feedback = createFeedbackElement(fileInput);
            const submitButton = findSubmitButton(fileInput);

            if (!file) {
                updateFeedback(feedback, "No file selected.", false, submitButton);
                return;
            }

            // Prepare the file data for validation
            const formData = new FormData();
            formData.append("name", file.name);
            formData.append("size", file.size);
            formData.append("tmp_name", file);

            try {
                const response = await fetch("/validate-file", {
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && result.valid) {
                    updateFeedback(feedback, "File is valid and ready to upload.", true, submitButton);
                } else {
                    updateFeedback(feedback, `Validation failed: ${result.message || "Unknown error"}`, false, submitButton);
                    fileInput.value = "";
                }
            } catch (error) {
                updateFeedback(feedback, `Error validating file: ${error.message}`, false, submitButton);
                fileInput.value = "";
            }
        });
    });

    // Create or find feedback element for a file input
    function createFeedbackElement(fileInput) {
        let feedback = fileInput.nextElementSibling;

        if (!feedback || !feedback.classList.contains("feedback")) {
            feedback = document.createElement("div");
            feedback.classList.add("feedback");
            fileInput.parentNode.insertBefore(feedback, fileInput.nextSibling);
        }

        return feedback;
    }

    // Find the nearest submit button in the form containing the file input
    function findSubmitButton(fileInput) {
        const form = fileInput.closest("form");
        return form ? form.querySelector('button[type="submit"]') : null;
    }

    // Update feedback message and manage the submit button state
    function updateFeedback(feedback, message, isValid, submitButton) {
        feedback.textContent = message;
        feedback.style.color = isValid ? "green" : "red";

        if (submitButton) {
            submitButton.disabled = !isValid;
        }
    }
});
