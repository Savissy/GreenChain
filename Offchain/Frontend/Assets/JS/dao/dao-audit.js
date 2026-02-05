import { fetchAudit } from "./dao-api.js";
import { formatDateTime } from "./dao-utils.js";

const proposalAudit = document.getElementById("proposalAudit");
const voteAudit = document.getElementById("voteAudit");

async function loadAudit() {
  const data = await fetchAudit();

  proposalAudit.innerHTML = "";
  voteAudit.innerHTML = "";

  if (!data.proposals || data.proposals.length === 0) {
    proposalAudit.innerHTML = "<p>No proposals on record.</p>";
  } else {
    data.proposals.forEach((proposal) => {
      const card = document.createElement("div");
      card.className = "dao-card";
      card.innerHTML = `
        <h3>${proposal.title}</h3>
        <div class="dao-meta">
          <span>Status: ${proposal.status}</span>
          <span>Created: ${formatDateTime(proposal.created_at)}</span>
        </div>
        <div class="dao-hash">Hash: ${proposal.proposal_hash}</div>
      `;
      proposalAudit.appendChild(card);
    });
  }

  if (!data.votes || data.votes.length === 0) {
    voteAudit.innerHTML = "<p>No votes on record.</p>";
  } else {
    data.votes.forEach((vote) => {
      const card = document.createElement("div");
      card.className = "dao-card";
      card.innerHTML = `
        <h4>Proposal #${vote.proposal_id}</h4>
        <div class="dao-meta">
          <span>Wallet: ${vote.wallet_address}</span>
          <span>Choice: ${vote.choice}</span>
          <span>Weight: ${vote.weight}</span>
          <span>Time: ${formatDateTime(vote.created_at)}</span>
        </div>
        <details>
          <summary>Signed message</summary>
          <pre>${vote.message || ""}</pre>
        </details>
      `;
      voteAudit.appendChild(card);
    });
  }
}

loadAudit();
