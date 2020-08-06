"use strict";

const host = `${location.href}/api/v1/`;

const noContentCodes = [];

export default async function (path, method = 'GET', data = null) {

    const init = {
        method,
        headers: {
            'Accept': 'application/json',
        }
    };

    if (data) {
        if (data instanceof FormData)
            init.body = data;
        else {
            init.body = JSON.stringify(data);
            init.headers = {
                ...init.headers,
                'Content-Type': 'application/json',
            };
        }
    }

    try {
        this.$bus.$emit('loading', true);

        const res = await fetch(`${host}${path}`, init);

        if (noContentCodes.includes(res.status))
            return {code: res.status};

        const json = await res.json();

        return {
            code: res.status,
            json
        };

    } catch (error) {
        return { error };
    } finally {
        this.$bus.$emit('loading', false);
    }

}
