{-# LANGUAGE DataKinds #-}
{-# LANGUAGE NoImplicitPrelude #-}
{-# LANGUAGE ScopedTypeVariables #-}
{-# LANGUAGE TemplateHaskell #-}
{-# LANGUAGE MultiParamTypeClasses #-}

module ProposalValidator where

import PlutusTx
import PlutusTx.Prelude
import Plutus.V2.Ledger.Api
import Plutus.V2.Ledger.Contexts

{-
  Proposal contract
  - register proposal hash
  - store proposer, id, timestamps, status
  - enforce Draft -> Active -> Closed -> Executed transitions
-}

newtype ProposalId = ProposalId Integer

PlutusTx.unstableMakeIsData ''ProposalId


data ProposalStatus = Draft | Active | Closed | Executed
PlutusTx.unstableMakeIsData ''ProposalStatus


data ProposalDatum = ProposalDatum
  { proposalId :: ProposalId
  , proposer :: PubKeyHash
  , proposalHash :: BuiltinByteString
  , createdAt :: POSIXTime
  , deadline :: POSIXTime
  , status :: ProposalStatus
  }

PlutusTx.unstableMakeIsData ''ProposalDatum

{-# INLINABLE validTransition #-}
validTransition :: ProposalStatus -> ProposalStatus -> Bool
validTransition Draft Active = True
validTransition Active Closed = True
validTransition Closed Executed = True
validTransition _ _ = False

{-# INLINABLE mkProposalValidator #-}
mkProposalValidator :: ProposalDatum -> ProposalStatus -> ScriptContext -> Bool
mkProposalValidator datum newStatus ctx =
    traceIfFalse "invalid status transition" (validTransition (status datum) newStatus)
    && traceIfFalse "deadline not reached" deadlineOk
  where
    info :: TxInfo
    info = scriptContextTxInfo ctx

    deadlineOk :: Bool
    deadlineOk = case (status datum, newStatus) of
      (Active, Closed) -> contains (from $ deadline datum) (txInfoValidRange info)
      _ -> True

validator :: Validator
validator = mkValidatorScript
  $$(PlutusTx.compile [|| mkProposalValidator ||])

validatorHash :: ValidatorHash
validatorHash = validatorHash validator

scriptAddress :: Address
scriptAddress = scriptHashAddress validatorHash
