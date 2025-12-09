export const formatPrice = (price) => {
    return parseFloat(price).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

export const formatAmount = (amount) => {
    return parseFloat(amount || 0).toLocaleString("en-US", {
        minimumFractionDigits: 8,
        maximumFractionDigits: 8,
    });
};
