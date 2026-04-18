# 💈 Barber Booking DDD

Um sistema de agendamento para barbearias desenvolvido com **Laravel 13** seguindo os princípios de **Domain-Driven Design (DDD)**. O objetivo principal é criar uma aplicação altamente escalável, testável e fácil de manter.

## 🎯 Contexto do Projeto
Este projeto simula o ecossistema de uma barbearia, onde clientes podem agendar horários com barbeiros específicos. A complexidade reside na gestão de horários, prevenção de conflitos e integração com serviços de pagamento e filas.

---

## 🏗️ Arquitetura e Escalabilidade

Para garantir que o sistema cresça sem virar um "monólito de lama", utilizamos as seguintes abordagens arquiteturais:

### 1. Domain-Driven Design (DDD)
Dividimos o código por **intenção** e não apenas por tecnologia. 
- **Lógica de Negócio Protegida:** As regras de ouro (ex: "um barbeiro não pode ter dois clientes ao mesmo tempo") ficam no `Domain`, isoladas de frameworks ou bancos de dados.
- **Linguagem Ubíqua:** O código usa os mesmos termos que o dono da barbearia usa (Appointment, TimeSlot, Barber).

### 2. Clean Architecture (Arquitetura Limpa)
O sistema é estruturado em camadas concêntricas onde a dependência aponta sempre para dentro:
- **Domain:** Entidades e Regras (Cérebro).
- **Application:** Casos de Uso (Trabalhador/Processos).
- **Infrastructure:** Ferramentas (Banco de dados, Filas, Pagamentos).
- **Interfaces:** Entrada de dados (API REST / Atendente).

### 3. Inversão de Dependência (DIP)
Usamos **Interfaces** para desacoplar as camadas. 
- **Por que escala?** Se amanhã precisarmos trocar o MySQL por MongoDB ou o gateway de pagamento Stripe por PayPal, alteramos apenas uma linha no `DomainServiceProvider`. O restante do sistema nem saberá da mudança.

---

## 🚀 Principais Vantagens

- **Facilidade de Entendimento:** Um novo desenvolvedor consegue entender o que o sistema faz apenas olhando a pasta `Application/UseCases`.
- **Testabilidade:** Como a lógica está isolada, podemos testar as regras de negócio sem precisar subir um banco de dados ou servidor web.
- **Pronto para Filas:** Processos pesados (como pagamentos) são despachados para filas de forma assíncrona, garantindo que o usuário nunca fique esperando.

---

## 🛠️ Como rodar o projeto

1. **Setup inicial:**
   ```bash
   composer run setup
   ```
2. **Desenvolvimento:**
   ```bash
   composer run dev
   ```

---

> *Para uma explicação lúdica da estrutura de pastas, veja o arquivo [ESTRUTURA.md](./ESTRUTURA.md).*
