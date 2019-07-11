const axios = require('axios');

async function retrieveAccessToken(username, password) {
    const authResponse = await axios({
        method: 'POST',
        url: 'https://api.codeship.com/v2/auth',
        headers: {
            'Accept': 'application/json'
        },
        auth: {
            username,
            password
        }
    });

    return authResponse.data.access_token;
}

async function triggerBuild(accessToken, organizationUuid, projectUuid, revision) {
    await axios({
        method: 'POST',
        url: `https://api.codeship.com/v2/organizations/${organizationUuid}/projects/${projectUuid}/builds`,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${accessToken}`
        },
        data: {
            ref: 'heads/master',
            commit_sha: revision
        }
    });
}

async function run(user, password, revision) {
    console.log(`Releasing revision "${revision}" via Codeship...`);
    console.log("Authenticating...");

    const accessToken = await retrieveAccessToken(user, password);

    console.log("Triggering...");

    await triggerBuild(
        accessToken,
        'f64f7300-e93d-0133-b53e-76bef8d7b14f',
        'df2056c0-3b1f-0136-0a27-1e274df65f02',
        revision
    )
}

run(
    process.env.CODESHIP_USER,
    process.env.CODESHIP_PASSWORD,
    process.env.REVISION
)
    .then(() => console.log("Finished..."))
    .catch(error => console.log(error));
