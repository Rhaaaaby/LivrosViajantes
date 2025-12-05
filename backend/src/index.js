import Fastify from "fastify";
import cors from "@fastify/cors";
import dotenv from "dotenv";
dotenv.config();

const fastify = Fastify({ logger: true });
fastify.register(cors, { origin: "*" });

fastify.get("/", async () => ({ message: "Servidor vivo e lendo livros!" }));

fastify.listen({ port: 4000 }, (err) => {
  if (err) throw err;
  console.log("🔥 Servidor rodando em http://localhost:4000");
});