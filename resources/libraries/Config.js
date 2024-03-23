const axiosPostHeader = () => {
    return {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'multipart/form-data'
        }
    }
}

export { axiosPostHeader }