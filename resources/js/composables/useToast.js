import { toast } from "vue-sonner";

/**
 * Composable for displaying toast notifications using vue-sonner
 * @returns {Object} Toast methods (success, error, warning, info, dismiss, toast)
 */
export function useToast() {
    const success = (message, options = {}) => {
        return toast.success(message, options);
    };

    const error = (message, options = {}) => {
        return toast.error(message, options);
    };

    const warning = (message, options = {}) => {
        return toast.warning(message, options);
    };

    const info = (message, options = {}) => {
        return toast.info(message, options);
    };

    const dismiss = (id) => {
        toast.dismiss(id);
    };

    return {
        success,
        error,
        warning,
        info,
        dismiss,
        toast, // Export raw toast function for advanced usage
    };
}

export default useToast;

