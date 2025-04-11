
const express = require("express");
const axios = require("axios");
const app = express();

app.use(express.json());

app.all("/proxy", async (req, res) => {
  const url = req.query.url;
  if (!url) return res.status(400).send("Missing URL");

  try {
    const response = await axios({
      method: req.method,
      url: url,
      headers: {
        "User-Agent": "Mozilla/5.0",
        ...(req.headers.authorization && { Authorization: req.headers.authorization }),
        ...(req.headers["content-type"] && { "Content-Type": req.headers["content-type"] })
      },
      data: req.body,
    });

    res.set("Content-Type", response.headers["content-type"] || "text/plain");
    res.status(response.status).send(response.data);
  } catch (err) {
    res
      .status(err.response?.status || 500)
      .send(err.response?.data || "Proxy error");
  }
});

app.use(express.static("public"));

const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log("Proxy running on port " + port);
});
