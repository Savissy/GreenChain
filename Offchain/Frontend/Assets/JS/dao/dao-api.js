const API_BASE = "/greenchain/api/governance";

export async function createProposal(payload) {
  return fetch(`${API_BASE}/create_proposal.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  }).then((res) => res.json());
}

export async function fetchProposals(status = "") {
  const url = status ? `${API_BASE}/get_proposals.php?status=${status}` : `${API_BASE}/get_proposals.php`;
  return fetch(url).then((res) => res.json());
}

export async function submitVote(payload) {
  return fetch(`${API_BASE}/submit_vote.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  }).then((res) => res.json());
}

export async function getMembership(wallet) {
  return fetch(`${API_BASE}/get_membership.php?wallet=${encodeURIComponent(wallet)}`)
    .then((res) => res.json());
}

export async function fetchTreasuryQueue() {
  return fetch(`${API_BASE}/get_treasury_queue.php`).then((res) => res.json());
}

export async function executeTreasury(payload) {
  return fetch(`${API_BASE}/execute_treasury.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  }).then((res) => res.json());
}

export async function fetchAudit() {
  return fetch(`${API_BASE}/get_audit.php`).then((res) => res.json());
}

export async function mintMember(payload) {
  return fetch(`${API_BASE}/mint_member.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  }).then((res) => res.json());
}

export async function burnMember(payload) {
  return fetch(`${API_BASE}/burn_member.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  }).then((res) => res.json());
}
